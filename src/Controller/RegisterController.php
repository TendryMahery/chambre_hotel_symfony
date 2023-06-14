<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, ManagerRegistry $manager, UserPasswordEncoderInterface $encode): Response
    {
        $utilisateur = new Utilisateur();
        $role = ["ROLE_USER"];
        $entityManager = $manager->getManager();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encode->encodePassword($utilisateur, $utilisateur->getMdp());
            $utilisateur->setMdp($password);
            $utilisateur->setRole($role);
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $this->addFlash('register', "felicitation vous etes inscrit");
            return $this->redirectToRoute('login');
        }
        return $this->render('register/register.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView(),
        ]);
    }
}
