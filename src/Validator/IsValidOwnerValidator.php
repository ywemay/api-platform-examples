<?php
namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidOwnerValidator extends ConstraintValidator
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* The first thing the validator does is interesting... it checks to see if
        the $value is, sort of, empty - if it's null. If it is null, instead of
        adding a validation error, it does the opposite! It returns.. which
        means that, as far as this validator is concerned, the value is valid.
        Why? The philosophy is that, if you want this field to be required, you
        should add an additional annotation to the property - the @Assert\NotBlank constraint.
        That means that our validator only has to do its job if there is a value set.
         */
        /* @var $constraint \App\Validator\IsValidOwner */
        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->context->buildViolation($constraint->anonymousMessage)
            // ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        // allow admin users to change owners
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (!$value instanceof User) {
            throw new \InvalidArgumentException('@IsValidOwner constraint must be put on a property containing a User object');
        }

        if ($value->getId() !== $user->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
