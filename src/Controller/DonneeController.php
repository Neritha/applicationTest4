<?php

namespace App\Controller;

use App\Entity\Donnee;
use App\Entity\Fichier;
use App\Form\AfficherType;
use App\Repository\DonneeRepository;
use App\Repository\FichierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DonneeController extends AbstractController
{
    /**
     * @Route("/tableauDeBord", name="tableauDeBord", methods={"GET","POST"})
     */
    public function tableauDeBord(DonneeRepository $repo, Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(AfficherType::class);

        $form->handleRequest($request);

        $dataAffiche = [];
        $donneesAffichees = [];

        $idF = 0;
        
        if ($form->isSubmitted() && $form->isValid()) {
            $fichierSelect = $form->getData()['fichier'];

            /////// Liste de touts les données qui ont été sélectinné
            $dataAffiche = [];
            for ($i = $fichierSelect->getPremiereDonnee()->getId(); $i <= ($fichierSelect->getPremiereDonnee()->getId())+($fichierSelect->getNbLigne())-2; $i++){
                $dataAffiche[] = $i;
            }
            $donneesAffichees = $repo->findBy(['id' => $dataAffiche]); // 
            $idF = $fichierSelect->getId();

        }

        return $this->render('donnee/tableauDeBord.html.twig', [
            'form' => $form->createView(),
            "listeDonnee" => $donneesAffichees,
            "idF" => $idF

        ]);
    }
    
    /**
     * @Route("/deleteDonnee/{id}", name="deleteDonnee", methods={"GET"})
     */
    public function deleteDonnee(Donnee $donnee, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        //voir si cette fonction est vraiment necessaire dans l'etats aqutuel des chose et 
        // basculer deteData de fichier controller ici
        
        $entityManager->remove($donnee);
        $entityManager->flush();

        return $this->redirectToRoute('tableauDeBord');
    }

    /**
     * @Route("/tableauDeBord/listeDonneesComplete/{idF}", name="listeDonneesComplete", methods={"GET"})
     */
    public function listeDonneesComplete(int $idF, DonneeRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $fichier = $entityManager->getRepository(Fichier::class)->find($idF);

        $donneesAffichees = [];

        $dataAffiche = [];

        for ($i = $fichier->getPremiereDonnee()->getId(); $i <= ($fichier->getPremiereDonnee()->getId())+($fichier->getNbLigne())-2; $i++){
            $dataAffiche[] = $i;
        }

        $donneesAffichees = $repo->findBy(['id' => $dataAffiche]);

        return $this->render('donnee/listeDonneesComplete.html.twig',[
            "listeDonnee" => $donneesAffichees,
            "fichier" => $fichier
        ]);
    }

     /**
     * @Route("/tableauDeBord/graphiques/{idF}", name="graphiques", methods={"GET"})
     */
    public function graphiques(int $idF, DonneeRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $fichier = $entityManager->getRepository(Fichier::class)->find($idF);

        $donneesAffichees = [];

        $dataAffiche = [];

        for ($i = $fichier->getPremiereDonnee()->getId(); $i <= ($fichier->getPremiereDonnee()->getId())+($fichier->getNbLigne())-2; $i++){
            $dataAffiche[] = $i;
        }

        $donneesAffichees = $repo->findBy(['id' => $dataAffiche]);

        return $this->render('donnee/graphiques.html.twig',[
            "listeDonnee" => $donneesAffichees,
            "fichier" => $fichier
        ]);
    }


     /**
     * @Route("/tableauDeBord/statistiques/{idF}", name="statistiques", methods={"GET"})
     */
    public function statistiques(int $idF, DonneeRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $fichier = $entityManager->getRepository(Fichier::class)->find($idF);

        $donneesAffichees = [];

        $dataAffiche = [];

        for ($i = $fichier->getPremiereDonnee()->getId(); $i <= ($fichier->getPremiereDonnee()->getId())+($fichier->getNbLigne())-2; $i++){
            $dataAffiche[] = $i;
        }

        $donneesAffichees = $repo->findBy(['id' => $dataAffiche]);

        return $this->render('donnee/statistiques.html.twig',[
            "listeDonnee" => $donneesAffichees,
            "fichier" => $fichier
        ]);
    }

}
