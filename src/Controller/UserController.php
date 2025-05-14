<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * 
     * @Route("/api/me", name="app_user_api_me")
     * 
     */
    public function apiMe()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');  
        return $this->json($this->getUser(), 200 , [], [
            'groups'=>['user:read']
        ]);
    }
}