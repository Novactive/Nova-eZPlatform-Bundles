<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Event\Subscriber;

use AlmaviaCX\Bundle\CaptchEtat\Challenge\ChallengeGenerator;
use AlmaviaCX\Bundle\CaptchEtat\Form\Type\CaptchEtatType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddCaptchaSubscriber implements EventSubscriberInterface
{
    protected ChallengeGenerator $challengeGenerator;

    public function __construct(
        ChallengeGenerator $challengeGenerator
    ) {
        $this->challengeGenerator = $challengeGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    public function onPreSetData(FormEvent $event): void
    {
        /** @var Form $form */
        $form = $event->getForm();

        if (!$form->isSubmitted()) {
            $form->add('captcha', CaptchEtatType::class, [
                'label' => 'customform.show.captcha',
                'error_bubbling' => false,
                'required' => true,
                'challenge' => ($this->challengeGenerator)(),
            ]);
        }
    }
}
