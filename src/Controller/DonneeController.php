<?php

namespace App\Controller;

use App\Entity\Donnee;
use App\Entity\Fichier;
use App\Form\AfficherType;
use App\Repository\DonneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DonneeController extends AbstractController
{
    /**
     * @Route("/tableauDeBord", name="tableauDeBord", methods={"GET","POST"})
     */
    public function TableauDeBord(DonneeRepository $repo, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(AfficherType::class);

        $form->handleRequest($request);

        $dataAffiche = [];
        $donneesAffichees = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            $fichierSelect = $form->getData()['fichier'];
            $idFichier = $fichierSelect->getId();
            $this->addFlash("success", "Le fichier choisi a pour ID : $idFichier");

            /////// Liste de touts les données qui ont été sélectinné
            $dataAffiche = [];
            for ($i = $fichierSelect->getPremiereDonnee()->getId(); $i <= ($fichierSelect->getPremiereDonnee()->getId())+($fichierSelect->getNbLigne())-2; $i++){
                $dataAffiche[] = $i;
                //$dataAffiche[] = $repo->findBy(['id' => $i]);
            }
            $donneesAffichees = $repo->findBy(['id' => $dataAffiche]); // pour avoir un tableau des differentes données à prendre en compt
    
            // for ($i = 144; $i <= 144+12; $i++){
            //     $dataAffiche[] = $i;
            // }
            ///////
            //dump($dataAffiche);

            return $this->render('donnee/tableauDeBord.html.twig', [
            'form' => $form->createView(),
            "listeDonnee" => $donneesAffichees
            
        ]);
        }

        return $this->render('donnee/tableauDeBord.html.twig', [
            'form' => $form->createView(),
            "listeDonnee" => $donneesAffichees
        ]);
    }
    
/////////////////////////////////////////////
    /**
     * @Route("/deleteDonnee/{id}", name="deleteDonnee", methods={"GET"})
     */
    public function deleteDonnee(Donnee $donnee, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->remove($donnee);
        $entityManager->flush();

        return $this->redirectToRoute('tableauDeBord');
    }

    // /**
    //  * @Route("/tableauDeBord", name="tableauDeBord", methods={"GET"})//,"POST"})
    //  */
    // public function TableauDeBord(Fichier $fichier = null,DonneeRepository $repo)//, Request $request)
    // {
    //     $this->denyAccessUnlessGranted('ROLE_USER'); // pour gérer les accès, si l'utilisateur n'as pas les droit il sera rediriger vers la page de login / il est aussi possible de gerer les accès au niveau des annotation avec celles des routes
        
    //     $donnees = $repo->findAll();
    //     // return $this->render('donnee/tableauDeBord.html.twig',[
    //     //     "lesDonnees" => $donnees
    //     // ]);
    //     //$dataAffiche = [];

    //     $form = $this->createForm(AfficherType::class, $fichier);

    //     //$form->handleRequest($request);
        
    //     if ($form->isSubmitted() && $form->isValid()){
    //         $idFichier = $fichier->getId();
    //         $this->addFlash("success", "le fichier choisi a pour ID : $idFichier");
    //         //return $this->redirectToRoute('about');
    //     }

    //     return $this->render('donnee/tableauDeBord.html.twig', [
    //         'form'=> $form->createView(),
            
    //         //"lesDonnees" => $donnees
    //     ]);
    // }






}
