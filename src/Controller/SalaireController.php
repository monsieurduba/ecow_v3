<?php

namespace App\Controller;

use App\Entity\Salaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SalaireController extends AbstractController
{
    #[Route('/salaire', name: 'app_salaire')]
    public function index(): Response
    {
        return $this->render('salaire/index.html.twig', [
            'controller_name' => 'SalaireController',
        ]);
    }

    #[Route('/salaire/ajout', name: 'salaire_add')]
    public function ajout(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {

            $date = $request->query->get('Date', (new \DateTime())->format('Y-m-d'));


            $salaire = new Salaire();
            $salaire->setUtilisateur($request->request->get('utilisateur'));
            $salaire->setMontant((float)$request->request->get('montant'));
            $salaire->setDate(new \DateTime($request->request->get('date')));
            $salaire->setDetail($request->request->get('detail'));

            $em->persist($salaire);
            $em->flush();

            $this->addFlash('success', 'Salaire ajouté avec succès !');

            return $this->redirectToRoute('salaire_add');
        }

        
        $date = new \DateTime($request->request->get('date'));

        // Récupération des 5 dernières dépenses
        $salaires = $em->getRepository(Salaire::class)
        ->findBy([], ['Date' => 'DESC'], 50);


        return $this->render('salaire/ajout.html.twig', [
            'salaires' => $salaires,
            'date' => $date
        ]);
    }

    #[Route('/salaire/supprimer/{id}', name: 'salaire_delete')]
    public function supprimer(int $id, EntityManagerInterface $em): Response
    {
        $salaire = $em->getRepository(Salaire::class)->find($id);

        if ($salaire) {
            $em->remove($salaire);
            $em->flush();
            $this->addFlash('danger', 'Salaire supprimé avec succès !');
        } else {
            $this->addFlash('warning', 'Salaire introuvable.');
        }

        return $this->redirectToRoute('salaire_add');
    }

}
