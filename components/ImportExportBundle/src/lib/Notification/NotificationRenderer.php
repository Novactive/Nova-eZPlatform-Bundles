<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Notification;

use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Core\Notification\Renderer\NotificationRenderer as NotificationRendererInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class NotificationRenderer implements NotificationRendererInterface
{
    protected RouterInterface $router;
    protected Environment $twig;

    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->router = $router;
    }

    public function render(Notification $notification): string
    {
        return $this->twig->render(
            '@ibexadesign/import_export/notification/default.html.twig',
            ['notification' => $notification]
        );
    }

    public function generateUrl(Notification $notification): ?string
    {
        if (array_key_exists('job_id', $notification->data)) {
            return $this->router->generate(
                'import_export.job.view',
                ['id' => $notification->data['job_id']]
            );
        }

        return null;
    }
}
