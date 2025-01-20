<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Validator;

use AlmaviaCX\Bundle\CaptchEtat\Challenge\ChallengeValidator;
use AlmaviaCX\Bundle\CaptchEtat\Validator\Constraint\CaptchEtatValidChallenge;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaptchEtatChallengeValidator extends ConstraintValidator
{
    protected ChallengeValidator $challengeValidator;

    /**
     * MCCCaptchaValidator constructor.
     */
    public function __construct(ChallengeValidator $challengeValidator, TranslatorInterface $translator)
    {
        $this->challengeValidator = $challengeValidator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CaptchEtatValidChallenge) {
            throw new UnexpectedTypeException($constraint, CaptchEtatValidChallenge::class);
        }

        if (!isset($value['uuid']) && !isset($value['captcha_code'])) {
            throw new UnexpectedValueException($value, 'array');
        }

        $captchaId = $value['uuid'];
        $answer = $value['captcha_code'];
        if (null === $answer || null === $captchaId) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        } elseif (!$this->challengeValidator->isValid($captchaId, $answer)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
