<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Validator\Constraint;

use AlmaviaCX\Bundle\CaptchEtat\Validator\CaptchEtatChallengeValidator;
use Symfony\Component\Validator\Constraint;

class CaptchEtatValidChallenge extends Constraint
{
    public $message = 'captchetat.form.answer.wrongAnswer';

    public function validatedBy()
    {
        return CaptchEtatChallengeValidator::class;
    }
}
