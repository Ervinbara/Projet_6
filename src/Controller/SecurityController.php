<?php

namespace App\Controller;

use App\Entity\User;
use Twig\Environment;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(
        Request $request,
        EntityManagerInterface $manager, 
        UserPasswordHasherInterface $encoder,
        MailerInterface $mailer,
        Environment $twig)
    {
        $user = new User();
        // Instantiation du formulaire de création de compte, et on relie les champs du formulaire aux champs d'un user
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            
            // Génération du token d'activation
            $user->setTokenActivation(md5(uniqid()));

            $manager->persist($user);
            $manager->flush(); 

            // Envoi du mail d'activation du compte à l'utilisateur
            $email = (new Email())
                ->to('ervinbara17@gmail.com')
                ->subject('Time for Symfony Mailer!') 
                ->text('Sending emails is fun again!')
                ->html($twig->render('emails/activation.html.twig', ['token' => $user->getTokenActivation()] ));
            $mailer->send($email);

            // Message flash 
            $this->addFlash('success', 'Le compte est crée, vous allez recevoir un e-mail pour valider votre compte.');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */   
    public function activation(EntityManagerInterface $manager, $token, UserRepository $user)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $user->findOneBy(['token_activation' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if(!$user){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // Récupération de la date du jour
        $date = new \DateTime(); 

        if($date > $user->getTokenExpiration()){
            throw new \RuntimeException('Le Token n\'est plus valide');
            // TODO : Proposer un ré-envoi de mail d'activation
        }
        // On supprime le token
        $user->setActive(true);
        $user->resetTokenActivation();
        $manager->flush();

        // On génère un message
        $this->addFlash('message', 'Compte activé avec succès');

        // On retourne à l'accueil
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig',[]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
