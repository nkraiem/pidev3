<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Entity\Panier;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler,
                             AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager ,\Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // generer un activation token
            $user->setActivationToken(md5(uniqid()));



            $entityManager->persist($user);
            $entityManager->flush();



            // do anything else you need here, like send an email
            $message=(new \Swift_Message('Activation de votre compte'))
                ->setFrom('eslord00gaming@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig',['token'=>$user->getActivationToken()]
                    ),'text/html'
                );
            $mailer->send($message);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route ("/activation/{token}", name="activation")
     */
    public function activation($token ,UserRepository $repository)
    {
        $user = $repository->findOneBy(['activation_token'=>$token]);
        if(!$user){
            throw $this->createNotFoundException("cet etulisateur nexiste pas");
        }
        $user->setActivationToken(null);
        $em=$this->getDoctrine()->getManager();
        $em->flush();
        $this->addFlash('message','vous avez bien activé votre compte');
        return $this->redirectToRoute('app_login');

    }
}
