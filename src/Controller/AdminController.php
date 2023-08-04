<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\Chambre;
use App\Entity\Personne;
use App\Entity\PersonneSearch;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChambreType;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ChambreRepository;
use App\Form\UserControlType;
use App\Repository\UtilisateurRepository;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use App\Form\PersonneSearchType;
use App\Repository\PersonneRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/chambre", name="admin_chambre")
     * @Route("/admin/edit/{id}", name="edit")
     */
    public function ajout(Chambre $chambre = null, Request $request, ManagerRegistry $manager, ChambreRepository $repository): Response
    {
        if ($chambre == null) {
            $chambre = new Chambre();
        }

        $personne_search = new PersonneSearch();
        $entityManager = $manager->getManager();
        $form = $this->createForm(ChambreType::class, $chambre);
        $form_search = $this->createForm(PersonneSearchType::class, $personne_search);
        $form->handleRequest($request);
        $id = $chambre->getId();
        $etat = $chambre->getEtat();
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $chambre->getId();
            if (!$id) {
                $chambre->setCreatedAt(new \DateTimeImmutable());
                $ajout = true;
            } else {
                $ajout = false;
            }
            $entityManager->persist($chambre);
            $entityManager->flush($chambre);
            if ($ajout == true) {
                $this->addFlash('success', "bien ajouté la chambre");
            } elseif ($ajout == false) {

                $this->addFlash('success', "bien modifié la chambre");
            }
            return $this->redirectToRoute('admin_chambre');
        }
        $chambres = $repository->findAll();
        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView(),
            'form_search' => $form_search->createView(),
            'chambre' => $chambres,
            'edit' => $chambre->getId() !== null,
            'recherche' => false

        ]);
    }

    /**
     * 
     *@Route("admin/liberer/{id}", name="liberer")
     */
    public function liberer(Chambre $chambre, ManagerRegistry $manager): Response
    {
        $entityManager = $manager->getManager();
        $personne = $chambre->getPersonne();
        $entityManager->remove($personne);
        $chambre->setEtat(false);
        $entityManager->flush();
        return $this->redirectToRoute('admin_chambre');
    }

    /**
     * @Route("/admin/supprimer/{id}", name="supprimer")
     */
    public function supprimer(ManagerRegistry $manager, Chambre $chambre): Response
    {
        $entityManager = $manager->getManager();
        $entityManager->remove($chambre);
        $entityManager->flush();
        $this->addFlash('suppression', "suppression avec succés");
        return $this->redirectToRoute('admin_chambre');
    }

    /**
     * @Route("/admin/actu", name="admin_actu")
     * @Route("/admin/actu/edit/{id}", name="edit_actu")
     */
    public function actu(Actualite $actualite = null, Request $request, ManagerRegistry $manager, ActualiteRepository $actualiteRepository): Response
    {
        if (!$actualite) {
            $actualite = new Actualite();
        }
        $image = $actualite->getImageFile();
        $entityManager = $manager->getManager();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$actualite->getId()) {
                $actualite->setCreatedAt(new \DateTimeImmutable('now'));
            } elseif ($actualite->getImageFile() == null) {
                $actualite->setImageFile($image);
            }

            $entityManager->persist($actualite);
            $entityManager->flush();
            $this->addFlash('modification_actu', "la modification a ete fait avec succes");
            return $this->redirectToRoute('admin_actu');
        }
        return $this->render('admin/adminActu.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView(),
            'actualite' => $actualiteRepository->findAll(),

        ]);
    }

    /**
     * @Route("admin/delete/actu/{id}", name="delete_actu")
     */
    public function deleteActu(Actualite $actualite, ManagerRegistry $manager)
    {
        $entityManager = $manager->getManager();
        if ($actualite) {
            $entityManager->remove($actualite);
            $entityManager->flush();
            $this->addFlash('suppression_actu', "la suppresion a ete fait avec succes");
            return $this->redirectToRoute('admin_actu');
        }
    }


    /**
     * @Route("/admin/user/", name="admin_user")
     */
    public function adminUser(UtilisateurRepository $repository)
    {
        $users = new Utilisateur();
        $user = $repository->findAll();
        $form = $this->createForm(UserControlType::class);
        return $this->render('admin/adminUser.html.twig', [
            'controller_name' => 'admin_user',
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    /**
     * @Route("/admin/user/{id}", name="modifier")
     */
    public function modifier(Utilisateur $users, Request $request, ManagerRegistry $manager, UtilisateurRepository $repository)
    {
        $entityManager = $manager->getManager();
        $form = $this->createForm(UserControlType::class, $users);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($users);
            $entityManager->flush();
            return $this->redirectToRoute('admin_user');
        }
        return $this->render('admin/adminUser.html.twig', [
            'controller_name' => 'admin_user',
            'form' => $form->createView(),
            'user' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/recherche", name="recherche")
     */

    public function recherche(PersonneRepository $personneRepository): Response
    {
        $personneSearch = new PersonneSearch();
        $personne =  $personneRepository->findPersonne($personneSearch);

        return $this->render('admin/recherche.html.twig', [
            'controller_name' => 'recherche',
            'recherche' => true,
            'personne' => $personne,

        ]);
    }
}
