<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Actualite;
use App\Repository\ActualiteRepository;

class ActualiteController extends AbstractController
{
    /**
     * @Route("/actualite", name="actualite")
     */
    public function index(ActualiteRepository $actualiteRepository): Response
    {
        return $this->render('actualite/actualite.html.twig', [
            'controller_name' => 'ActualiteController',
            'actualite' => $actualiteRepository->findAll(),
        ]);
    }
}
