<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core\Collection;

use Symfony\Component\HttpClient\HttpClient;

final class RemoteTags
{
    public function __invoke(string $component): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', "https://api.github.com/repos/Novactive/NovaeZ{$component}/tags");

        return $response->toArray(false);
    }
}
