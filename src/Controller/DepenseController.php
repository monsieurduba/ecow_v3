<?php

namespace App\Controller;

use App\Entity\Depense;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DepenseController extends AbstractController
{
    #[Route('/depenses/{date}', name: 'depenses_list', methods: ['GET'], defaults: ['date' => null])]
    public function list(Request $request,EntityManagerInterface $entityManager, ?string $date): Response
    {

        $date = $request->query->get('date'); // Récupérer le mois depuis la requête GET

        $queryBuilder = $entityManager->getRepository(Depense::class)->createQueryBuilder('e');
        $monthlySummary = $date;
        $totalGeneral = 0;
        $thibaultShare = 0;
        $lisaShare = 0 ;
        $thibaultPaid = 0;
        $lisaPaid = 0;
        $thibaultEcart = 0;
        $lisaEcart = 0;
        $message = "";

        if ($date) {
            
            $start = (new \DateTime($date))->modify('first day of this month')->format('Y-m-d');
            
            $end = (new \DateTime($date))->modify('last day of this month')->format('Y-m-d');
            //dd($start);
            //dd($end);
            $queryBuilder->where('e.Date BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

            // Récupérer les dépenses du mois sélectionné avec le total par utilisateur
            $query = $entityManager->createQuery(
            'SELECT e.utilisateur, SUM(e.montant) as total 
            FROM App\Entity\Depense e 
            WHERE e.Date BETWEEN :startDate AND :endDate
            GROUP BY e.utilisateur'
            )->setParameters([
                'startDate' => $start,
                'endDate' => $end
            ]);
            $monthlySummary = $query->getResult(); 

            // Initialiser les montants payés par chacun
            foreach ($monthlySummary as $summary) {
                if ($summary['utilisateur'] === 'Thibault') {
                    $thibaultPaid = $summary['total'];
                } elseif ($summary['utilisateur'] === 'Lisa') {
                    $lisaPaid = $summary['total'];
                }
            }
            // Calcul du total général
            $totalGeneral = $thibaultPaid + $lisaPaid;

            // Calcul de la répartition des dépenses
            $thibaultShare = $totalGeneral * 0.54;
            $lisaShare = $totalGeneral * 0.46;
            
            // Calcul de l'équilibrage
            $lisaEcart = $lisaShare - $lisaPaid;
            $thibaultEcart = $thibaultShare - $thibaultPaid;

            $balance = ($thibaultEcart - $lisaEcart)/2;

            if ($balance < 0) {
                $message = "Lisa doit transférer " . number_format(abs($balance), 2, ',', ' ') . " € à Thibault.";
            } elseif ($balance > 0) {
                $message = "Thibault doit transférer " . number_format(abs($balance), 2, ',', ' ') . " € à Lisa.";
            } else {
                $message = "Les comptes sont équilibrés.";
            }

            // Calcul du total général des dépenses
            $totalGeneral = array_sum(array_column($monthlySummary, 'total'));

        }
        

        $depenses = $queryBuilder->getQuery()->getResult();
        
        return $this->render('depense/list.html.twig', [
            'depenses' => $depenses,
            'selectedDate' => $date,
            'monthlySummary' => $monthlySummary,
            'totalGeneral' => $totalGeneral,
            'thibaultShare' => $thibaultShare,
            'lisaShare' => $lisaShare,
            'thibaultPaid' => $thibaultPaid,
            'lisaPaid' => $lisaPaid,
            'lisaEcart' => $lisaEcart,
            'thibaultEcart' => $thibaultEcart,
            'message' => $message
        ]); 
    }



    #[Route('/form', name: 'depense_form', methods: ['POST','GET'])]
    public function form(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $request->query->get('utilisateur', 'Thibault');
        $date = $request->query->get('Date', (new \DateTime())->format('Y-m-d'));


        // Récupération des 5 dernières dépenses
        $recentesDepenses = $entityManager->getRepository(Depense::class)
        ->findBy([], ['Date' => 'DESC'], 50);


        if ($request->isMethod('POST')) {
            $utilisateur = $request->request->get('utilisateur');
            $montant = (float) $request->request->get('montant');
            $detail = $request->request->get('detail');
            $date = new \DateTime($request->request->get('date'));

            // Vérification des données reçues
            if (!$utilisateur || !$montant || !$date) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('depense_ajout');
            }
    
            // Création et enregistrement de la dépense
            $depense = new Depense();
            $depense->setUtilisateur($utilisateur);
            $depense->setMontant($montant);
            $depense->setDetail($detail);
            $depense->setDate($date);

            $entityManager->persist($depense);
            $entityManager->flush();

            // Ajout d'un message de confirmation
            $this->addFlash('success', 'Dépense ajoutée avec succès.');

            
            // Récupération des 5 dernières dépenses
            $recentesDepenses = $entityManager->getRepository(Depense::class)
            ->findBy([], ['Date' => 'DESC'], 50);
            $this->addFlash('success', 'Dépense ajoutée avec succès !');

            // Redirection vers la page d'ajout avec les dernières dépenses
            return $this->render('depense/ajout.html.twig', [
                'utilisateur' => $utilisateur,
                'date' => $date,
                'recentes_depenses' => $recentesDepenses
            ]);
        }

        return $this->render('depense/ajout.html.twig', [
            'utilisateur' => $utilisateur,
            'date' => $date,
            'recentes_depenses' => $recentesDepenses
        ]);
    }

    #[Route('/supprimer/{id}', name: 'depense_supprimer', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $depense = $entityManager->getRepository(Depense::class)->find($id);
    
        if (!$depense) {
            throw $this->createNotFoundException('Dépense introuvable.');
        }
    
        // Suppression de la dépense
        $entityManager->remove($depense);
        $entityManager->flush();
    
        // Récupération de la page précédente pour rediriger correctement
        $referer = $request->headers->get('referer');
        $this->addFlash('danger', 'Dépense supprimée avec succès !');

        return $this->redirect($referer ?: $this->generateUrl('depense_form'));
    }
    
}
