<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    /**
     *
     * @Route(path="/{page}", name="home", requirements={"page"="\d+"})
     */
    public function index(FigureRepository $repo, Request $request, int $page = 0)
    {
        // Par défaut afficher les 10 figures les plus récentes
        $figures = $repo->findBy([], ['id' => 'DESC'], 9, $page);
        $page = $page + 8;

        // Récupération du nombre total de tricks pour établir une limite lors du clique sur le bouton voir plus
        $total_tricks = count($repo->findAll());

        if ($request->isXmlHttpRequest()) {

            return $this->render('figure/tricks.html.twig', [
                'figures' => $figures,
                'page' => $page
            ]);
        } else {
            return $this->render('figure/index.html.twig', [
                'figures' => $figures,
                'page' => $page,
                'total_tricks' => $total_tricks
            ]);
        }
    }

    /**
     * @Route(path="/forum/{coms}", name="forum", requirements={"coms"="\d+"})
     */
    public function forum(Request $request, EntityManagerInterface $manager, CommentRepository $repo, int $coms = 0)
    {
        $comment = new Comment();

        // On récupère tout les commentaire qui ne sont pas attribué à une figure càd ou figure_id == NULL
        $commentForum = $repo->findby(['figure' => NULL], ['id' => 'DESC'], 4, $coms);
        $coms = $coms + 4;

        // Récupération du nombre total de tricks pour établir une limite lors du clique sur le bouton voir plus
        $total_comments = count($repo->findby(['figure' => NULL]));

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTimeImmutable())
                ->setUser($this->getUser()); //Tu correspond à la figure que j'ai en variable
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Message envoyé !');

            return $this->redirectToRoute('forum', [
            ]);
        }

        if ($request->isXmlHttpRequest()) {

            return $this->render('figure/comments_forum.html.twig', [
                'commentForm' => $form->createView(),
                'comments' => $commentForum,
                'coms' => $coms,
                'total_comments' => $total_comments
            ]);
        } else {
            return $this->render('figure/forum.html.twig', [
                'commentForm' => $form->createView(),
                'comments' => $commentForum,
                'coms' => $coms,
                'total_comments' => $total_comments
            ]);
        }

    }

    /**
     * Ajout et modification de tricks
     * @Route("/new/figure", name="add_figure")
     * @Route("/edit/figure/{id}/edit", name="edit_figure")
     */
    public function form(Figure $figure = null, Request $request, EntityManagerInterface $manager)
    {
        $newFigure = $figure === null;
        if ($newFigure) {
            $figure = new Figure();
        }

        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();
            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

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

            if ($newFigure) {
                $manager->persist($figure);
            } else {
                $figure->setModifyAt(new \DateTimeImmutable());
            }
            $manager->flush();
            $this->addFlash('success', 'Figure ajouté !');

            return $this->redirectToRoute('figure_show', ['id' => $figure->getId()]);
        }

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
    public function figureShow(Figure $figure, Request $request, EntityManagerInterface $manager, FigureRepository $repo)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTimeImmutable())
                ->setFigure($figure)
                ->setUser($this->getUser()); //Tu correspond à la figure que j'ai en variable
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Message envoyé !');

            return $this->redirectToRoute('figure_show', [
                'id' => $figure->getId()
            ]);
        }

        $figure = $repo->find($figure);
        return $this->render('figure/figure_show.html.twig', [
            'figure' => $figure,
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'une figure
     * @Route("/delete/figure/{id}/delete", name="delete_figure")
     */
    public function deleteFigure(Figure $figure)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($figure);
        $em->flush();

        return $this->redirectToRoute('home');
    }


    /**
     * Suppression d'une image liée à un trick
     * @Route("/supprime/image/{id}", name="figure_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete', $data['_token'])) {
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
