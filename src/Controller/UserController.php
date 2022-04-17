<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends BaseController
{
    /**
     * @Route("/api/me", name="app_user_api_me")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function apiMe(): Response
    {
        return $this->json($this->getUser(), 200, [], [
            'groups' => ['user:read'],
        ]);
    }
}
