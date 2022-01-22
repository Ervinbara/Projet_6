<?php

namespace App\Controller;

use App\Repository\CommentRepository;
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
    public function index(FigureRepository $repo, Request $request)
    {
        $page = (int)$request->query->get('page',1);
//        dd($page);
        // Par défaut afficher les 8 figures les plus récentes
        $figures = $repo->findBy([],['id'=> 'ASC'],10);
        // if on appui sur load more
            // On va récupérer 4 figuree de plus
                // Afficher le tout en temps réel sans rechargement de la page (AJAX needed)
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
            'figures' => $figures
        ]);
    }

    /**
     * @Route("/forum", name="forum")
     */
    public function forum(Request $request, EntityManagerInterface $manager, CommentRepository $repo)
    {
        $comment = new Comment();

        // On récupère tout les commentaire qui ne sont pas attribué à une figure càd ou figure_id == NULL
        $commentForum = $repo->findby(['figure'=> NULL],['id' =>'DESC']);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new DateTimeImmutable())
                ->setUser($this->getUser()); //Tu correspond à la figure que j'ai en variable
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Message envoyé !');

            return $this->redirectToRoute('forum',[
            ]);
        }

        return $this->render('figure/forum.html.twig', [
            'commentForm' => $form->createView(),
            'comments' => $commentForum
        ]);
    }

    /**
     * Ajout et modification de tricks
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
     * Affichage d'un trick en particulier
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
        return $this->render('figure/figure_show.html.twig',[
            'figure' => $figure,
            'commentForm' => $form->createView(),
        ]);
    }


    /**
     * Suppression d'une image liée à un trick
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
     * Suppression d'une vidéo liée à un trick
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
