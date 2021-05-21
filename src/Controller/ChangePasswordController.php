<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Message\UserUpdatePasswordCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ChangePasswordController extends AbstractController
{
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/change-password", name="app_change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $passwordEncoder->encodePassword($this->getUser(), $data['plain_password']);

            $payload = [
                'id'       => (string) $this->getUser()->getId(),
                'password' => $password,
            ];
            $metadata = [
                'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
                'client_ip'       => $request->getClientIp(),
                'timestamp'       => (new \DateTime())->format('c'),
            ];

            $this->commandBus->dispatch(new UserUpdatePasswordCommand($payload, $metadata));

            $this->addFlash('success', 'password updated');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('change_password/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
