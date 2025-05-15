<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\QrCode\QrCodeGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig',[
            'error'=>$authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }
    
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(AuthenticationUtils $authenticationUtils): Response
    {
      throw new \Exception('logout() should never be reached. ');
    }

    /**
     * @Route("/authenticate/2fa/enable",name="app_2fa_enable")
     */
    public function enable2fa(TotpAuthenticatorInterface $totpAuthenticator, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user=$this->getUser();
        if(!$user->isTotpAuthenticationEnabled()){
            $user->setTotpSecret($totpAuthenticator->generateSecret());
            $entityManager->flush();
        }
        
        return $this->render('security/enable2f.html.twig');
    }
 
    /**
     * @Route("/authentication/2fa/qr-code",name="app_qr_code")
     */

     public function displayGoogleAuthenticatorQrCode(QrCodeGenerator $qrCodeGenerator)
     {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $qrCode = $qrCodeGenerator->getTotpQrCode($this->getUser());

        return new Response($qrCode->writeString(),200,['Content-type' => 'image/png']);
     }

}
