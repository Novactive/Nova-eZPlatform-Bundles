<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Tests;

use Facebook\WebDriver\WebDriverDimension;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Crawler as BaseCrawler;

final class BrowserHelper
{
    private array $lastRequest = [];

    private BaseCrawler $lastCrawler;

    public function __construct(private readonly Client $client)
    {
        $this->client->getWebDriver()->manage()->window()->setSize(new WebDriverDimension(1200, 1000));
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function request(
        string $method,
        string $uri,
        bool $force = false,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true,
        bool $isHTML = true
    ): BaseCrawler {
        $request = [
            'method' => $method,
            'uri' => $uri,
            'parameters' => $parameters,
            'files' => $files,
            'server' => $server,
            'content' => $content,
            'changeHistory' => $changeHistory,
        ];

        if ($request === $this->lastRequest) {
            if (false === $force) {
                return $this->lastCrawler;
            }
        }

        $this->lastRequest = $request;

        $crawler = $this
            ->client
            ->request(
                $method,
                $uri,
                $parameters,
                $files,
                $server,
                $content,
                $changeHistory
            );

        if(!$isHTML) {
            $crawler = new BaseCrawler(
                $this->client->getWebDriver()->getPageSource(),
                $this->client->getCurrentURL()
            );
        }

        $this->lastCrawler = $crawler;

        return $this->lastCrawler;
    }

    public function getXML(string $url): BaseCrawler
    {
        return $this->request('GET', $url, isHTML: false);
    }

    public function get(string $url): BaseCrawler
    {
        return $this->request('GET', $url);
    }

    public function crawler(): BaseCrawler
    {
        $this->client->refreshCrawler();

        return $this->client->getCrawler();
    }

    public function waitFor(string $locator): BaseCrawler
    {
        $this->lastCrawler = $this->client->waitFor($locator);

        return $this->lastCrawler;
    }

    public function wait(int $numberOfSeconds): void
    {
        sleep($numberOfSeconds);
    }
}
