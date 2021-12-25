<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Images;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FigureController extends AbstractController
{
     /**
     * @Route("/", name="home")
     */
    public function index(FigureRepository $repo)
    {
        $figures = $repo->findAll();       
        // dd($figures);
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
            'figures' => $figures
        ]);
    }

    /**
     * @Route("/figure/new", name="add_figure")
     * @Route("/figure/{id}/edit", name="edit_figure")
     */
    public function form(Figure $figure = null, Request $request, EntityManagerInterface $manager)
    {
        if(!$figure) {
            $figure = new Figure();
        }
        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();
            
            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $figure->addImage($img);
            }
        
            $manager->persist($figure);
            $manager->flush();
        
            return $this->redirectToRoute('figure_show', ['id' => $figure->getId()]);
        }

        return $this->render('figure/edit_figure.html.twig', [
            'controller_name' => 'FigureController',
            'form' => $form->createView(),
            'figure' => $figure
        ]);
    }


    /**
     * TODO : Voir pourquoi après le clique sur le lien "Voir plus", il y a une erreur d'affichage
     * @Route("/show_figure/{id}", name="figure_show")
     */
    public function figure_show(Figure $figure, Request $request, EntityManagerInterface $manager,FigureRepository $repo)
    {
        $figure = $repo->find($figure);
        return $this->render('figure/figure_show.html.twig',[
            'figure' => $figure,
        ]);
    }


    /**
     * @Route("/supprime/image/{id}", name="figure_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request){
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
