<?php

namespace App\Controller;

use App\Form\ChangeEmailFormType;
use App\Form\SettingFormType;
use App\Message\UserUpdateEmailCommand;
use App\Message\UserUpdateSettingCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;

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
    public function editEmail(Request $request): Response
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

    /**
     * @Route("/settings", name="app_setting")
     */
    public function settings(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(SettingFormType::class, [
            'locale'   => $this->getUser()->getLocale(),
            'timezone' => $this->getUser()->getTimezone(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $payload = [
                'id'       => (string) $this->getUser()->getId(),
                'locale'   => $data['locale'],
                'timezone' => $data['timezone'],
            ];
            $metadata = [
                'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
                'client_ip'       => $request->getClientIp(),
                'timestamp'       => (new \DateTime())->format('c'),
            ];

            $this->commandBus->dispatch(new UserUpdateSettingCommand($payload, $metadata));

            $this->addFlash('success', 'settings updated');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('profile/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
