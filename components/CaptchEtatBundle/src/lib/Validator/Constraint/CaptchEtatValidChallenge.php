<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Validator\Constraint;

use AlmaviaCX\Bundle\CaptchEtat\Validator\CaptchEtatChallengeValidator;
use Symfony\Component\Validator\Constraint;

class CaptchEtatValidChallenge extends Constraint
{
    public string $message = 'captchetat.form.answer.wrongAnswer';

    public function validatedBy(): string
    {
        return CaptchEtatChallengeValidator::class;
    }
}
