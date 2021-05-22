<?php

namespace App\Controller;

use App\Message\UserCreateCommand;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $uuid = (string) Uuid::v6();
            $cmd = new UserCreateCommand([
                'id'          => $uuid,
                'email'       => $user->getEmail(),
                'password'    => $user->getPassword(),
                'invite_code' => $form->get('invite_code')->getData(),
            ], [
                'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
                'client_ip'       => $request->getClientIp(),
                'timestamp'       => (new \DateTime())->format('c'),
            ]);
            $this->commandBus->dispatch($cmd);

            $this->addFlash('success', 'account created');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
