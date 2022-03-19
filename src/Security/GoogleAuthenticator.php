<?php

namespace App\Security;

use App\Entity\Historique;
use App\Entity\Panier;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Created by IntelliJ IDEA
 * User : mert
 * Date : 12/18/17
 * Time: 12:00 PM
 */
class GoogleAuthenticator extends \KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator
{

    private $clientRegistry;
    private $em;
    private $router;

    public function  __construct(ClientRegistry $clientRegistry,EntityManagerInterface  $em , RouterInterface $router)
    {
        $this->clientRegistry=$clientRegistry;
        $this->em=$em;
        $this->router=$router;

    }


    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->getPathInfo()=='/connect/google/check'&&$request->isMethod('GET');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
       return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);
        $email = $googleUser->getEmail();
        $user =$this->em->getRepository('App:User')
            ->findOneBy(['email'=>$email]);
        if(!$user)
        {
            $historique=new Historique();
            $historique->setNbS(0);
            $historique->setNbG(0);
            $historique->setNbB(0);
            $historique->setNbE(0);
            $historique->setNbTotal(0);
            $historique->setNbTG(0);
            $user=new User();
            $user->setEmail($googleUser->getEmail());
            $user->setPreference('empty');
            $user->setStatus('true');
            $user->setName($googleUser->getName());
            $user->setLastName($googleUser->getLastName());
            $user->setPassword('qzertysa250');
            $user->setHistorique($historique);
            $user->setExpiriencePoint(0);
            $user->setImage($googleUser->getAvatar());

            $panier= new Panier();
            $panier->setUser($user);
            $panier->setPrix(0);
            $this->em->persist($user);
            $this->em->flush();
            $this->em->persist($panier);
            $this->em->flush();

        }
        return $user;
    }

    /**
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    private function getGoogleClient(): \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient('google');
    }
    /**
     * @inheritDoc
     * @param Request $request The request that resulted in AuthenticationException
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $authException The exception that started
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function start(Request $request,\Symfony\Component\Security\Core\Exception\AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    /**
     * @inheritDoc
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception)
    {
        // TODO: Implement onAuthenticationFailure() method.
    }

    /**
     * @inheritDoc
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $providerKey)
    {
        // TODO: Implement onAuthenticationSuccess() method.
    }




}