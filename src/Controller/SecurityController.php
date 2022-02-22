<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Environment;

class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(
        Request                     $request,
        EntityManagerInterface      $manager,
        UserPasswordHasherInterface $encoder,
        TokenGeneratorInterface     $tokenGenerator,
        MailerInterface             $mailer,
        Environment                 $twig)
    {
        $user = new User();
        // Instantiation du formulaire de création de compte, et on relie les champs du formulaire aux champs d'un user
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            // Génération du token d'activation
            $token = $tokenGenerator->generateToken();
            $user->setTokenActivation($token);


            $manager->persist($user);
            $manager->flush();

            // Envoi du mail d'activation du compte à l'utilisateur
            $email = (new Email())
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html($twig->render('emails/activation.html.twig', ['token' => $user->getTokenActivation()]));
            $mailer->send($email);

            // Message flash 
            $this->addFlash('success', 'Le compte est crée, vous allez recevoir un e-mail pour valider votre compte.');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
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
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // Récupération de la date du jour
        $date = new \DateTime();

        // Si la date du jour où l'on clique sur le lien d'activation est supérieur à la date du token d'expiration, le token n'est plus valide
        if ($date > $user->getTokenExpiration()) {
            $this->addFlash('warning', 'Le Token n\'est plus valide');
            return $this->redirectToRoute('security_token_expiration');
        }
        // On supprime le token
        $user->setActive(true);
        $user->resetTokenActivation();
        $manager->flush();

        // On génère un message
        $this->addFlash('success', 'Compte activé avec succès');
        return $this->redirectToRoute('security_login');
    }


    /**
     * @Route("/forgot_password", name="security_forgot_password")
     */
    public function forgotPass(
        Request                 $request,
        UserRepository          $user,
        MailerInterface         $mailer,
        EntityManagerInterface  $manager,
        TokenGeneratorInterface $tokenGenerator): Response
    {
        // On initialise le formulaire
        $form = $this->createForm(ResetPasswordType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $user->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('warning', 'Cette adresse e-mail est inconnue');

                // On retourne sur la page de connexion
                return $this->redirectToRoute('security_forgot_password');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On écrit le token en base de données
            try {
                $user->setTokenActivation($token);
                $manager->persist($user);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('security_login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('security_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // On génère l'e-mail
            $email = (new Email())
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->text('Bonjour, Une demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant : ' . $url);
            $mailer->send($email);

            // On crée le message flash de confirmation
            $this->addFlash('success', 'E-mail de réinitialisation du mot de passe envoyé !');

            // On redirige vers la page de login
            return $this->redirectToRoute('security_login');
        }

        // On envoie le formulaire à la vue
        return $this->render('security/forgot_password.html.twig', ['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/token_expiration", name="security_token_expiration")
     */
    public function tokenExpiration(
        Request                 $request,
        UserRepository          $user,
        MailerInterface         $mailer,
        EntityManagerInterface  $manager,
        Environment             $twig,
        TokenGeneratorInterface $tokenGenerator): Response
    {
        // On initialise le formulaire
        $form = $this->createForm(ResetPasswordType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $user->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('warning', 'Cette adresse e-mail est inconnue');

                // On retourne sur la page de connexion
                return $this->redirectToRoute('security_token_expiration');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'écrire le token en base de données
            try {
                $user->setTokenActivation($token);
                $manager->persist($user);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('security_login');
            }

            // Envoi du mail d'activation du compte à l'utilisateur
            $email = (new Email())
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html($twig->render('emails/activation.html.twig', ['token' => $user->getTokenActivation()]));
            $mailer->send($email);

            // On crée le message flash de confirmation
            $this->addFlash('success', 'E-mail d\'activation de compte envoyé !');

            // On redirige vers la page de login
            return $this->redirectToRoute('security_login');
        }

        // On envoie le formulaire à la vue
        return $this->render('security/token_expiration.html.twig', ['emailForm' => $form->createView()]);
    }


    /**
     * Page afficher après le clique sur le lien dans le mail
     * @Route("/reset_password/{token}", name="security_reset_password")
     */
    public function resetPassword(
        Request                     $request,
        string                      $token,
        UserRepository              $user,
        UserPasswordHasherInterface $encoder,
        EntityManagerInterface      $manager)
    {
        // On cherche un utilisateur avec le token donné
        $user = $user->findOneBy(['token_activation' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('warning', 'Token Inconnu');
            return $this->redirectToRoute('security_login');
        }

        // Si le formulaire est envoyé en méthode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setTokenActivation(null);

            // On chiffre le mot de passe
            $user->setPassword($encoder->hashPassword($user, $request->request->get('password')));

            // On stocke en base
            $manager->persist($user);
            $manager->flush();

            // On crée le message flash
            $this->addFlash('success', 'Mot de passe mis à jour !');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('security_login');
        } else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig', []);
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
