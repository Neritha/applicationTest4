<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Donnee;
use App\Entity\Fichier;
use App\Form\AjoutFichierType;
use App\Repository\FichierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FichierController extends AbstractController
{
    /**
     * @Route("/ajoutFichier", name="ajoutFichier")
     */
    public function ajoutFichier(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
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
     return $this->render('fichier/ajoutFichier.html.twig', [
            'form'=> $form->createView()
        ]);
    }


    /**
     * @Route("/fichierSupr/{id}", name="fichierSupr", methods={"GET"})
     */
    public function suprFichier(Fichier $fichier, EntityManagerInterface $manager): Response
    {
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
        // foreach ($ids as $id) {
        //     $donnee = $this->getDoctrine()->getRepository(Donnee::class)->find($id);

        //     if ($donnee) {
        //         $this->getDoctrine()->getManager()->remove($donnee);
        //     }
        // }

        // $this->getDoctrine()->getManager()->flush();

        // foreach($ids as $valueId){
        //     $this->forward('App\Controller\YourController::deleteDonnee', ['id' => $valueId]);
        // }
        $entityManager->remove($donnee);
        $entityManager->flush();
        
    }


    // /**
    //  * @Route('/supprimerDonnee/{id}', name: 'supprimerDonnee', methods: ['GET'])
    //  */
    // public function supprimerDonnee(Donnee $donnee, EntityManagerInterface $manager)
    // {
    //     $manager->remove($donnee);
    //     $manager->flush();
    // }

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
     *@Route("/ajoutDonnee/{id}", name="ajoutDonnee", methods={"GET","POST"})
     */
    public function addData (int $id, FichierRepository $repo, Donnee $data = null, Request $request, EntityManagerInterface $manager)
    {
        $fichier = $repo->find($id);

        $fileDirectory = $this->getParameter('file_directory'); // recuperer le nom du nom_serveur
        $filePath = $fileDirectory . '/' . $fichier->getNomServeur();  // recuperer le chemin du fichier grêce au nom sur le serveur

        $csvContents = $this->readCsvFile($filePath); // nous avont ici un tableau des donées du fichier

        // il suffit à présent de prendre chaque lignes du tableau pour les ajouter comme un nouvel élément data à la base de données

        $premiereLigne = true;

        foreach (array_slice($csvContents, 1) as $value) { //les lignes qui commence à la seconde pour ne pas prendre en compt les titres des colonnes
            // Création d'une nouvelle entité Data pour chaque ligne du fichier CSV
            $data = new Donnee();

            $data->setTemps(floatval($value[0]));  // [les colonnes]
            $data->setT(floatval($value[1]));
            $data->setH(floatval($value[2])) ;
            $data->setV(floatval($value[3]));
            $data->setPuissance(floatval($value[4]));
            $data->setTds(intval($value[5]));
            $data->setPh(floatval($value[6]));
            $data->setPgf(floatval($value[7]));
            $data->setPr(floatval($value[8]));
            $data->setVfe(floatval($value[9]));
            $data->setVnc(floatval($value[10]));

            $manager->persist($data);

            if ($premiereLigne){
                // ajouter la première donnée au tableau des fichier dans la colonne des premières données
                $fichier->setPremiereDonnee($data);
            }
            
            $premiereLigne = false;
        }

        $manager->flush();

        // ajouter le rang de la première donnée à la table des fichiers (cela pourra aussi servir d'indicateur si le fichier est dans le base ou non (griser le boutton correspondant en fonction ("ajouter à la base")))
    
        $this->addFlash("success", "Les données ont été ajouté !");

        return $this->redirectToRoute('ajoutFichier');
    }
}
    