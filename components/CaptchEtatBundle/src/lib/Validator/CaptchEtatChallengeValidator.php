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
        dump($value);
        die('222');

        if (!isset($value['captcha_id']) && !isset($value['answer'])) {
            throw new UnexpectedValueException($value, 'array');
        }

        $captchaId = $value['captcha_id'];
        $answer = $value['answer'];
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
