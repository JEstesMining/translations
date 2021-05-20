<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResettingController extends AbstractController
{
    // display form, submit, send email if email found, redirect to checkEmail
    public function request(Request $request): Response
    {
        return $this->render('resetting/request.html.twig', [
        ]);
    }

    // display "Check Email" page
    public function checkEmail(Request $request): Response
    {
        return $this->render('resetting/checkEmail.html.twig', [
        ]);
    }

    // reset based on the provided token
    public function reset(Request $request, string $token): Response
    {
        return $this->render('resetting/checkEmail.html.twig', [
        ]);
    }
}
