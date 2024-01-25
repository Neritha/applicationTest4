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
        $donnees = $repo->findAll();
        return $this->render('donnee/tableauDeBord.html.twig',[
            "lesDonnees" => $donnees
        ]);
    }
}
