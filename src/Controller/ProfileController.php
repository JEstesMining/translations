<?php

namespace App\Controller;

use App\Form\ChangeEmailFormType;
use App\Message\UserUpdateEmailCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/edit-email", name="app_edit_email")
     */
    public function editEmail(Request $request, ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ChangeEmailFormType::class, ['email' => $this->getUser()->getEmail()], [
            'em' => $this->getDoctrine()->getManager(),
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $payload = [
                'id'    => (string) $this->getUser()->getId(),
                'email' => $data['email'],
            ];
            $metadata = [
                'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
                'client_ip'       => $request->getClientIp(),
                'timestamp'       => (new \DateTime())->format('c'),
            ];

            $this->commandBus->dispatch(new UserUpdateEmailCommand($payload, $metadata));

            $this->addFlash('success', 'email updated');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('profile/edit_email.html.twig', [
            'form' => $form->createView(),
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
