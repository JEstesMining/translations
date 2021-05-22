<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Message\PasswordResetRequestCommand;
use App\Message\UserUpdatePasswordCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResettingController extends AbstractController
{
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/reset-password", name="app_resetting_request")
     */
    public function request(Request $request): Response
    {
        // @todo Limit the amount of requests similar to login
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $entity = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                'email' => strtolower($data['email']),
            ]);
            if ($entity) {
                $payload = [
                    'id' => (string) $entity->getId(),
                ];
                $metadata = [
                    'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
                    'client_ip'       => $request->getClientIp(),
                    'timestamp'       => (new \DateTime())->format('c'),
                ];
                $this->commandBus->dispatch(new PasswordResetRequestCommand($payload, $metadata));
            }

            $this->addFlash('success', 'email has been sent with password reset instructions');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetting/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="app_resetting_reset")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token): Response
    {
        $entity = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'confirmationToken' => $token,
        ]);
        if (null === $entity) {
            $this->addFlash('danger','invalid request');

            return $this->redirectToRoute('app_resetting_request');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $passwordEncoder->encodePassword($entity, $data['plain_password']);

            $payload = [
                'id'       => $entity->getId(),
                'password' => $password,
            ];
            $metadata = [
                'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
                'client_ip'       => $request->getClientIp(),
                'timestamp'       => (new \DateTime())->format('c'),
            ];
            // @todo Make this a different cmd and reset the token/pw requested at fields
            $this->commandBus->dispatch(new UserUpdatePasswordCommand($payload, $metadata));

            $this->addFlash('success', 'password reset');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetting/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
