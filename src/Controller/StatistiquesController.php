<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\Salaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques/{utilisateur}', name: 'stats_user')]
    public function stats(string $utilisateur, Request $request, EntityManagerInterface $em): Response
    {
        // Récupération du paramètre mois (format YYYY-MM), sinon mois courant
        $mois = $request->query->get('mois', (new \DateTime())->format('Y-m'));

        // Début et fin du mois sélectionné
        $dateDebut = \DateTime::createFromFormat('Y-m-d', $mois . '-01');
        $dateFin = (clone $dateDebut)->modify('last day of this month')->setTime(23, 59, 59);

        // 📊 Total salaires filtrés
        $salaireRepo = $em->getRepository(Salaire::class);
        $salaireTotal = $salaireRepo->createQueryBuilder('s')
            ->select('SUM(s.Montant)')
            ->where('s.Utilisateur = :u')
            ->andWhere('s.Date BETWEEN :start AND :end')
            ->setParameter('u', $utilisateur)
            ->setParameter('start', $dateDebut)
            ->setParameter('end', $dateFin)
            ->getQuery()
            ->getSingleScalarResult();

        // 📊 Dépenses par typologie (détail) filtrées
        $depenseRepo = $em->getRepository(Depense::class);
        $depensesParTypo = $depenseRepo->createQueryBuilder('d')
            ->select('t.nom as type, SUM(d.montant) as total')
            ->join('d.type', 't') // Jointure avec l'entité TypeDepense
            ->where('d.utilisateur = :u')
            ->andWhere('d.Date BETWEEN :start AND :end')
            ->setParameter('u', $utilisateur)
            ->setParameter('start', $dateDebut)
            ->setParameter('end', $dateFin)
            ->groupBy('t.id') // ou t.nom
            ->getQuery()
            ->getResult();


        // 📊 Total dépenses
        $totalDepenses = array_sum(array_column($depensesParTypo, 'total'));


        // 📊 Total dépenses
        $totalDepenses = array_sum(array_column($depensesParTypo, 'total'));

        // 📊 Épargne = salaire - dépenses
        $epargne = $salaireTotal - $totalDepenses;

        return $this->render('statistiques/index.html.twig', [
            'utilisateur' => $utilisateur,
            'salaireTotal' => $salaireTotal,
            'depensesParTypo' => $depensesParTypo,
            'totalDepenses' => $totalDepenses,
            'mois' => $mois,
            'epargne' => $epargne,
        ]);
    }
}
