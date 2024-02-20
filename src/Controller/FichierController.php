<?php

namespace App\Controller;

use DateTime;
use App\Entity\Data;
use App\Entity\Donnee;
use App\Entity\Fichier;
use App\Form\AjoutFichierType;
use App\Repository\FichierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use phpDocumentor\Reflection\PseudoTypes\StringValue;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FichierController extends AbstractController
{
    /**
     * @Route("/ajoutFichier", name="ajoutFichier")
     */
    public function ajoutFichier(Request $request, EntityManagerInterface $entityManagerInterface, PaginatorInterface $paginator, FichierRepository $repo, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // $session->set('progress', 0);
        $progress = 0;

        $form = $this->createForm(AjoutFichierType::class);
        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                
                $fichier = $form->get('fichier')->getData();
               
                if($fichier){
                 $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                    $nomFichier = $nomFichier.'-'.uniqid().'.'.$fichier->guessExtension();    
                    try{      
                        $f = new Fichier();
                        $f->setNomServeur($nomFichier);
                        $f->setNomOriginal($fichier->getClientOriginalName());
                        $f->setDateEnvoi(new \Datetime());
                        $f->setExtension($fichier->guessExtension());
                        $f->setTaille($fichier->getSize());
                        $f->setProprietaire($this->getUser());

                        // compter le nombre de ligne
                        $fichierDataCsv = fopen($fichier,"r");

                        $nbLigne = 0;
                        while (!feof($fichierDataCsv)) {
                            fgets($fichierDataCsv); // Lire chaque ligne sans la stocker
                            $nbLigne++;
                        }
                        
                        fclose($fichierDataCsv);
                        // Fin du comptage du nombre de lignes

                        $f->setNbLigne($nbLigne);                  
                       
                        $fichier->move($this->getParameter('file_directory'), $nomFichier);

                        $entityManagerInterface->persist($f);
                        $entityManagerInterface->flush();  

                        $this->addFlash('success', 'Le fichier à bien été envoyer à l\'application ');
                        
                    }
                    catch(FileException $e){
                        $this->addFlash('warning', 'Erreur d\'envoi');
                    }   
                }
                return $this->redirectToRoute('ajoutFichier');
            }
        }     
        
        $fichiers = $paginator->paginate(
            $repo->listeFichierComplete(),
            $request->query->getInt('page', 1), 
            7
        );

     return $this->render('fichier/ajoutFichier.html.twig', [
            'form'=> $form->createView(),
            'lesFichiers'=>$fichiers,
            'progress' => $progress
        ]);
    }


    /**
     * @Route("/fichierSupr/{id}", name="fichierSupr", methods={"GET"})
     */
    public function suprFichier(Fichier $fichier, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $idDonneeSupr=null ;

        if ($fichier->getPremiereDonnee()){ 

            for ($i = $fichier->getPremiereDonnee()->getId(); $i <= $fichier->getPremiereDonnee()->getId()+ $fichier->getNbLigne()-2; $i++){
                $idDonneeSupr[] = $i;
            }
        }
        $fileDirectory = $this->getParameter('file_directory');
                $filePath = $fileDirectory . '/' . $fichier->getNomServeur();

                if (file_exists($filePath)) {
                    unlink($filePath); // supprime physiquement le fichier
                }

                $manager->remove($fichier);

        if ($idDonneeSupr != null){

            foreach ($idDonneeSupr as $valueId){

                $donnee = $this->getDoctrine()->getRepository(Donnee::class)->find($valueId);

                $this->deleteDonnees($donnee, $manager);

             } 
        }

        
        $manager->flush();

        $this->addFlash("success", "Le fichier a bien été supprimé!");

        return $this->redirectToRoute('ajoutFichier');

    } 

    public function deleteDonnees(Donnee $donnee, EntityManagerInterface $entityManager): void
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager->remove($donnee);
        $entityManager->flush();
        
    }


    /**
     * @Route("/fichierContenu/{id}", name="fichierContenu", methods={"GET"})
     */
    public function fichierContenu(int $id,FichierRepository $repo): Response
    {
        $fichier = $repo->find($id);

        $fileDirectory = $this->getParameter('file_directory'); // recuperer le nom du nom_serveur
        $filePath = $fileDirectory . '/' . $fichier->getNomServeur();  // recuperer le chemin du fichier grêce au nom sur le serveur

        $csvContents = $this->readCsvFile($filePath); // utiliser la fonction plus bas

        return $this->render('fichier/fichierContenu.html.twig', [
            'leFichier' => $fichier,
            'csvContents' => $csvContents,
        ]);

    } 

    private function readCsvFile(string $filePath): array // cette fonction lit le contenu d'un fichier csv et le range dans un tableau
    // cette fonction ouvre et ferme un fichier de la même manière que dans AppFixtures.php
    {
        $csvContents = [];

        $fichierDataCsv = fopen($filePath,"r"); // basé sur AppFixture.php
        while (!feof($fichierDataCsv)){
            $csvContents[]=fgetcsv($fichierDataCsv);
        }
        fclose($fichierDataCsv);

        return $csvContents;
    }

    /**
     *@Route("/ajoutFichier/ajoutDonnee/{id}", name="ajoutDonnee", methods={"GET","POST"})
     */
    public function addData (int $id, FichierRepository $repo, Donnee $data = null, Request $request, EntityManagerInterface $manager, SessionInterface $session)
    {
        // $progress = 0;
        $compt = 0;

        $fichier = $repo->find($id);

        // $session->set('progress', 0); ////////////

        $fileDirectory = $this->getParameter('file_directory'); // recuperer le nom du nom_serveur
        $filePath = $fileDirectory . '/' . $fichier->getNomServeur();  // recuperer le chemin du fichier grêce au nom sur le serveur

        $csvContents = $this->readCsvFile($filePath); // nous avont ici un tableau des donées du fichier

        // il suffit à présent de prendre chaque lignes du tableau pour les ajouter comme un nouvel élément data à la base de données

        $premiereLigne = true;

        foreach (array_slice($csvContents, 1) as $value) { //les lignes qui commence à la seconde pour ne pas prendre en compt les titres des colonnes
            // Création d'une nouvelle entité Data pour chaque ligne du fichier CSV
            $data = new Donnee();

            $dateStr = strval($value[0]);
            $date = DateTime::createFromFormat("Y-m-d H:i:s", $dateStr);

            if ($date != false) {
                // La conversion de la date est réussie, affectez la date à l'entité Donnee
                $data->setDate($date);
            } else {
                // Gérer l'erreur si la conversion de la date échoue
                // Par exemple, enregistrer un journal d'événements ou ignorer cette ligne de données
                echo "Erreur lors de la conversion de la date pour la valeur : $dateStr  qui a pour type " . gettype($dateStr);
            }

            $data->setDate($date);
            $data->setT(floatval($value[1]));
            $data->setH(floatval($value[2])) ;
            $data->setV(floatval(0));
            $data->setPuissance(floatval($value[4]));
            $data->setTds(intval($value[5]));
            $data->setPh(floatval($value[6]));
            $data->setPgf(floatval($value[7]));
            $data->setPr(floatval($value[3]));
            $data->setVfe(floatval($value[8]));
            $data->setVnc(floatval(0));

            $manager->persist($data);

            if ($premiereLigne){
                // ajouter la première donnée au tableau des fichier dans la colonne des premières données
                $fichier->setPremiereDonnee($data);
            }
            
            $premiereLigne = false;

            //$this->updateProgress($session, 10); // Par exemple, 10% de progression à chaque itération

            // $session->set('progress', 10);

            // $progress++;
            $compt++;

            if ($compt == 30){
                $manager->flush();
                $compt = 0;
            }

            // $this->progressBar($progress);
        }

        if ($compt != 0){
            $manager->flush();
        }

        

        // ajouter le rang de la première donnée à la table des fichiers (cela pourra aussi servir d'indicateur si le fichier est dans le base ou non (griser le boutton correspondant en fonction ("ajouter à la base")))
    
        $this->addFlash("success", "Les données ont été ajouté !");

        return $this->redirectToRoute('ajoutFichier');
    }

    public function progressBar (int $i){
        return $this->render('fichier/ajoutFichier.html.twig', [
            'progress'=>$i
        ]);
    }
}
    