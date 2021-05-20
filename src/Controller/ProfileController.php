<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// @todo Fully Auth for all routes
class ProfileController extends AbstractController
{
    public function editEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profile/edit_email.html.twig', [
        ]);
    }

    public function editLocale(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profile/edit_locale.html.twig', [
        ]);
    }

    public function editTimezone(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profile/edit_timezone.html.twig', [
        ]);
    }
}
