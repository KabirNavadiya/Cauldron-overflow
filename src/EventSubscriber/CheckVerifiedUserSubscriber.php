<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;
    private SessionInterface $session;

    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router=$router;
        $this->session = $session;

    }
    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();
        if(!$passport instanceof UserPassportInterface){
            throw new \Exception('Unexpected passport type');   
        }

        $user = $passport->getUser();

        if(!$user instanceof User){
            throw new \Exception('Unexpected user type');
        }

        if(!$user->getIsVerified()){
            // throw new CustomUserMessageAuthenticationException(
            //     'Please verify your account before logging in.'
            // );

            throw new AccountNotVerifiedAuthenticationException();
        }
    }

    public function onLoginFalure(LoginFailureEvent $event)
    {
        if(!$event->getException() instanceof AccountNotVerifiedAuthenticationException){
            return;
        }

        // get user email who is trying to log in for resend-verify-user-url
        $request = $event->getRequest();
        $email = $request->request->get('email');
        $this->session->set('unverified_user_email', $email);

        $response = new RedirectResponse(
            $this->router->generate('app_verify_resend_email')
        );
        $event->setResponse($response);  
    }
    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginFailureEvent::class => 'onLoginFalure',
        ];
    }
}