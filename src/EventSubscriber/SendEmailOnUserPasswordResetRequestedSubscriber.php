<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\HandleTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 */
class SendEmailOnUserPasswordResetRequestedSubscriber implements EventSubscriberInterface
{
    private $manager;
    private $mailer;
    private $router;

    public function __construct(EntityManagerInterface $manager, MailerInterface $mailer, RouterInterface $router)
    {
        $this->manager = $manager;
        $this->mailer  = $mailer;
        $this->router  = $router;
    }

    public function onUserPasswordResetRequested(GenericEvent $event)
    {
        $subject  = $event->getSubject();
        $payload  = $subject->getPayload();
        $metadata = $subject->getMetadata();
        $entity = $this->manager->getRepository(User::class)->find($payload['id']);
        if (null === $entity) {
            return;
        }

        $resetUrl = $this->router->generate('app_resetting_reset', ['token' => $entity->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new Email())
            ->from('noreply@dascrypto.farm')
            ->to($entity->getEmail())
            ->subject('[DasCrypto.farm] Password Reset Request')
            ->text('Continue resetting your password at '.$resetUrl)
            ->html('<p>Continue resetting your password at <a href="'.$resetUrl.'">'.$resetUrl.'</a></p>')
        ;
        $this->mailer->send($email);
    }

    public static function getSubscribedEvents()
    {
        return [
            'user.password_reset_requested' => 'onUserPasswordResetRequested',
        ];
    }
}
