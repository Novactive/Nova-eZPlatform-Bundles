<?php

/**
 * eZ Platform Bundles Mono Repo Project.
 *
 * @author    Novactive - Sébastien Morel <s.morel@novactive.com> aka Plopix <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   MIT
 */

declare(strict_types=1);

namespace Novactive\RssFeedBundle\Tests;

use Novactive\eZPlatform\Bundles\Tests\BrowserHelper;
use Novactive\eZPlatform\Bundles\Tests\PantherTestCase;

class CreateRssFeedTest extends PantherTestCase
{
    private const FEED_URL_SLUG = 'testfeed';

    public function testFeedCreate(): void
    {
        $helper = new BrowserHelper($this->getPantherClient());

        // Logging in as Admin
        $crawler = $helper->get('/admin/login');
        $loginForm = $crawler->filter('form');
        $loginForm->form(
            [
                '_username' => 'admin',
                '_password' => 'publish',
            ]
        );
        $loginForm->submit();

        // Creating Feed
        $crawler = $helper->get('/admin/rssfeeds/add');
        $helper->waitFor('form');

        $addItemLink = $crawler->filter('#open-child-form');
        $addItemLink->click();

        $selectLocationButton = $crawler->filter('button.js-novaezrssfeed-select-location-id');
        $selectLocationButton->click();

        $helper->wait(2);

        $helper->waitFor('#react-udw');
        $ezPlatformSpan = $crawler->filter('span[data-original-title="Ibexa Platform"]');
        $ezPlatformSpan->click();

        $helper->client()->waitForEnabled('button.c-actions-menu__confirm-btn');
        $confirmButton = $crawler->filter('button.c-actions-menu__confirm-btn');
        $confirmButton->click();

        $rssForm = $crawler->filter('form.ibexa-form');

        /** @var \Symfony\Component\Panther\DomCrawler\Form $form */
        $form = $rssForm->form();
        $form->setValues(
            [
                'rss_feeds[title]' => 'Test Feed',
                'rss_feeds[description]' => 'Test Description',
                'rss_feeds[url_slug]' => self::FEED_URL_SLUG,
                'rss_feeds[feed_sites]' => ['site'],
            ]
        );

        $form['rss_feeds[feed_items][0][contenttype_id]']->select('2');
        $helper->wait(2);
        $form['rss_feeds[feed_items][0][title]']->select('title');
        $form['rss_feeds[feed_items][0][subtree_path][location]']->getValue();

        $rssForm->submit();

        $helper->wait(2);

        // Checking the Anonymous role policy, adding it if it's missing
        $helper->get('/admin/role/1');
        $source = $helper->client()->getPageSource();
        if (false === \stripos($source, 'role.policy.rss')) {
            $policyCrawler = $helper->get('/admin/role/1/policy/create');
            $helper->waitFor('form');
            $policyForm = $policyCrawler->filter('form');
            $helper->wait(2);
            $policyForm->form()['policy_create[policy]']->select('rss|read');
            $policyForm->submit();
        }

        // Testing the Feed content
        $helper->get('/rss/feed/site/'.self::FEED_URL_SLUG);
        $source = $helper->client()->getPageSource();
        self::assertStringContainsString('Test Feed', $source);

        // deleting the created feed
        $listCrawler = $helper->get('/admin/rssfeeds/');
        $listCrawler->filter('#rssfeed-'.self::FEED_URL_SLUG)->filter('button.rssfeed-delete')->click();
        $helper->waitFor('form#form-delete-rss-feed-'.self::FEED_URL_SLUG);
        $listCrawler->filter('form#form-delete-rss-feed-'.self::FEED_URL_SLUG)->submit();
    }
}
