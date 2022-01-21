<?php

namespace App\Controller;

use DateTime;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Comment;
use App\Form\FigureType;
use App\Form\CommentType;
use App\Repository\FigureRepository;
use DateTimeImmutable;
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
        $newFigure = $figure === null;
        if($newFigure) {
            $figure = new Figure();
        }
        
        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

//            $videos = $form->get('videos')->getData();
//
//             foreach($videos as $video){
//                $vdo = new Videos();
//                $vdo->setName($videos);
//                $figure->addVideo($vdo);
//
//             }

            
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
            
            if($newFigure) {
                $manager->persist($figure);
            }
            $manager->flush();
            $this->addFlash('success', 'Figure ajouté !');

            return $this->redirectToRoute('figure_show', ['id' => $figure->getId()]);
        }
//        dd($form->createView());

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
        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new DateTimeImmutable())
                    ->setFigure($figure)
                    ->setUser($this->getUser()); //Tu correspond à la figure que j'ai en variable
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Message envoyé !');

            return $this->redirectToRoute('figure_show',[
                'id' =>$figure->getId()
            ]);
        } 

        $figure = $repo->find($figure);
        // dd($figure->getComments())
        return $this->render('figure/figure_show.html.twig',[
            'figure' => $figure,
            'commentForm' => $form->createView(),
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

    /**
     * @Route("/supprime/video/{id}", name="figure_delete_video", methods={"DELETE"})
     */
    public function deleteVideo(Videos $video, Request $request){
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$video->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $video->getName();
            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($video);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
