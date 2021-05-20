<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordController extends AbstractController
{
    public function changePassword(Request $request): Response
    {
        // @todo fully auth
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('change_password/change_password.html.twig', [
        ]);
    }
}
