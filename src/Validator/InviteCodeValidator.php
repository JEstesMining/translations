<?php

namespace App\Validator;

use App\Entity\Invite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InviteCodeValidator extends ConstraintValidator
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\InviteCode */

        if (null === $value || '' === $value) {
            return;
        }

        $entity = $this->manager->getRepository(Invite::class)->findOneBy([
            'code' => $value,
        ]);
        if (null === $entity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
