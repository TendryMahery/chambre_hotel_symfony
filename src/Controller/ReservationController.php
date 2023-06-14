<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Personne;
use App\Repository\ChambreRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PersonneType;

class ReservationController extends AbstractController
{
    /**
     * @Route("/admin/reservation/{id}", name="reservation")
     */
    public function reservation(Chambre $chambre, Request $request, ManagerRegistry $manager): Response
    {
        $disponibilite = $chambre->getEtat();
        if ($disponibilite == true) {
            $this->addFlash('nonDisponible', "cette chambre n'est pas disponible");
            return $this->redirectToRoute('admin_chambre');
        } else {
            $personne = new Personne();
            $entityManager = $manager->getManager();
            $form = $this->createForm(PersonneType::class, $personne);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $disponibilite = true;
                $chambre->setEtat($disponibilite);
                $personne->setChambre($chambre);
                $entityManager->persist($personne);
                $entityManager->flush();
                return $this->redirectToRoute('admin_chambre');
            }
        }
        return $this->render('reservation/reservation.html.twig', [
            'controller_name' => 'ReservationController',
            'chambre' => $chambre,
            'form' => $form->createView(),
        ]);
    }
}
