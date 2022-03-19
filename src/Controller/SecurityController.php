<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\ProduitPanier;
use App\Entity\User;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use http\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('backindex');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        return $this->redirectToRoute('app_login');

    }
    /**
     * @Route ("/oubli_pass", name="app_forgoten_password")
     */
    public function reset(Request $request,UserRepository $repository,\Swift_Mailer $mailer,TokenGeneratorInterface $tokenGenerator){
        //on cree le formulaire
        $form=$this->createForm(ResetPassType::class);
        //on traite le formulaire
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            $donner=$form->getData();
            $user=$repository->findOneBy(['email'=>$donner['email']]);
            //si l'utilisateur n'existe pas
            if(!$user)
            {
                $this->addFlash('danger','cette adresse n\' existe pas');
                return $this->redirectToRoute('app_login');
            }
            //on genere l'url
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $em=$this->getDoctrine()->getManager();
                $em->flush();

            }catch (\Exception $e){
                $this->addFlash('warning','une erreur est servenue'.$e->getMessage());
                return $this->redirectToRoute('app_login');
            }
            $url=$this->generateUrl('App_reset_password',['token'=>$token],
            UrlGeneratorInterface::ABSOLUTE_URL);
            $message=(new \Swift_Message('mot de pass oublier'))
                ->setFrom('eslord00gaming@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                   "<p>Bonjour </p></p>une demande de renitialisation de mot de pass a ete effectuer pour votre compte 
                   dans ESLORD vouillez  cliquer sur le lien suivant :" .$url. '</p>',
                   'text/html');
            $mailer->send($message);
            $this->addFlash('message','le mail de renitialsation de mot de passe a ete envoyer');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/resetpass.html.twig', [
            'EmailForm' => $form->createView(),
        ]);
    }
    /**
     * @Route ("/reset-pass/{token}", name="App_reset_password")
     */
    public function resetpass($token ,Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user=$this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token'=>$token]);
        if(!$user){
            $this->addFlash('danger','Token inconnu');
            return $this->redirectToRoute('app_login');
        }
        if($request->isMethod('POST')){
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user,$request->get('password')));
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('message','Mot de passe modifie avec succés');
            return $this->redirectToRoute('app_login');
        }else{
            return $this->render('security/password.html.twig',['token'=>$token]);
        }

    }

    /**
     *  @param Security $sec
     * @Route ("/smartlogout", name="smartlogout")
     */
    public function smartlogout(Security $sec)
    {
        $user= $sec->getUser();
        $panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$user]);
        $pps=$this->getDoctrine()->getRepository(ProduitPanier::class)->findBy(['panier'=>$panier[0]]);
        if($pps==null){
            return $this->redirectToRoute('app_logout');
        }
        else{
            return $this->redirectToRoute('viderPanier');
        }

    }
}
