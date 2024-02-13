<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    // /**
    //  * @Route("/admin/user", name="app_admin_user")
    //  */
    // public function index(): Response
    // {
    //     $this->denyAccessUnlessGranted('ROLE_ADMIN');
    //     return $this->render('admin/user/index.html.twig', [
    //         'controller_name' => 'UserController',
    //     ]);
    // }

    
    /**
     * @Route("/admin/user", name="admin_user", methods={"GET"})
     */
    public function listeUtilisateursAdmin (UserRepository $repo, PaginatorInterface $paginator, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $utilisateurs = $paginator->paginate(
            $repo->listeUserCompleteAdmin(),
            $request->query->getInt('page', 1), 
            10
        );

        return $this->render("admin/user/user.html.twig", [
            "lesUtilisateurs" => $utilisateurs
        ]);
    }

    /**
     * @Route("/admin/user/fichiers", name="admin_user_fichiers", methods={"GET"})
     */
    public function listeFichierUtilisateursAdmin (UserRepository $repo, PaginatorInterface $paginator, Request $request) : Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // $utilisateurs = $paginator->paginate(
        //     $repo->listeUserCompleteAdmin(),
        //     $request->query->getInt('page', 1), 
        //     9
        // );

        return $this->render("admin/user/userListeFichier.html.twig");//, [
          //  "lesUtilisateurs" => $utilisateurs
        //]);
    }

}
