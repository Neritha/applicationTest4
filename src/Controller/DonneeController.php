<?php

namespace App\Controller;

use App\Repository\DonneeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DonneeController extends AbstractController
{
    /**
     * @Route("/tableauDeBord", name="tableauDeBord", methods={"GET"})
     */
    public function TableauDeBord(DonneeRepository $repo)
    {
        $this->denyAccessUnlessGranted('ROLE_USER'); // pour gérer les accès, si l'utilisateur n'as pas les droit il sera rediriger vers la page de login / il est aussi possible de gerer les accès au niveau des annotation avec celles des routes
        $donnees = $repo->findAll();
        return $this->render('donnee/tableauDeBord.html.twig',[
            "lesDonnees" => $donnees
        ]);
    }
}
