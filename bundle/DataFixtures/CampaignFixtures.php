<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\DataFixtures;

use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Novactive\Bundle\eZMailingBundle\Entity\Broadcast;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\StatHit;

/**
 * Class CampaignFixtures.
 */
class CampaignFixtures extends Fixture implements DependentFixtureInterface
{
    const FIXTURE_COUNT_CAMPAIGN = 10;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 1; $i <= self::FIXTURE_COUNT_CAMPAIGN; ++$i) {
            $campaign = new Campaign();
            $campaign->setNames(
                [
                    'fre-FR' => $faker->words(2, true),
                    'eng-GB' => $faker->words(2, true),
                    'eng-US' => $faker->words(2, true),
                ]
            );
            $campaign->setReportEmail($faker->email);
            $campaign->setSenderEmail($faker->email);
            $campaign->setReturnPathEmail($faker->email);
            $campaign->setSenderName($faker->name);
            $campaign->setLocationId(2);
            // create MailingLists
            $nbDestinations = $faker->numberBetween(0, MailingListFixtures::FIXTURE_COUNT_MAILINGLIST);
            for ($j = 0; $j <= $nbDestinations; ++$j) {
                $mailingListIndex = $faker->numberBetween(1, MailingListFixtures::FIXTURE_COUNT_MAILINGLIST);
                $campaign->addMailingList($this->getReference("mailing-list-{$mailingListIndex}"));
            }

            // create Mailing
            $nbMailings = $faker->numberBetween(1, 10);
            for ($k = 0; $k < $nbMailings; ++$k) {
                $mailing = new Mailing();
                $mailing->setNames(
                    [
                        'fre-FR' => $faker->words(2, true),
                        'eng-GB' => $faker->words(2, true),
                        'eng-US' => $faker->words(2, true),
                    ]
                );
                $mailing->setStatus($faker->randomElement(Mailing::STATUSES));
                $mailing->setRecurring($faker->boolean());
                $mailing->setHoursOfDay([$faker->numberBetween(0, 23)]);
                $mailing->setDaysOfMonth([$faker->numberBetween(0, 31)]);
                $mailing->setLocationId(2);
                $mailing->setSiteAccess('site');
                $mailing->setSubject('Subject '.$faker->words(2, true));
                $campaign->addMailing($mailing);

                // 80% d'avoir un
                if ($faker->boolean(20)) {
                    continue;
                }

                // Create 2 broadcasts
                $nbBroadcasts = $faker->numberBetween(1, 2);
                for ($l = 0; $l < $nbBroadcasts; ++$l) {
                    $broadcast = new Broadcast();
                    $broadcast->setEmailSentCount($faker->numberBetween(0, 500));
                    $startDate = Carbon::instance($faker->dateTimeThisYear);
                    $endDate   = clone $startDate;
                    $endDate->addMinutes(15);
                    $broadcast->setStarted($faker->dateTimeThisYear);
                    $broadcast->setEnded($endDate);
                    $broadcast->setHtml("Fixture {$i}{$k}{$l}");
                    $mailing->addBroadcast($broadcast);

                    // create Stats Hit
                    $nbHits = $faker->numberBetween(0, 100);
                    for ($m = 0; $m < $nbHits; ++$m) {
                        $key = $faker->uuid;
                        $hit = new StatHit();
                        $hit->setUserKey($key);
                        $hit->setUrl('-');
                        $hit->setBrowserName(
                            $faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Internet Explorer'])
                        );
                        $hit->setOsName($faker->randomElement(['Mac OS X', 'Windows', 'Linux']));
                        $hit->setBroadcast($broadcast);
                        $manager->persist($hit);
                        $nbSubHits = $faker->numberBetween(0, 5);
                        for ($n = 0; $n < $nbSubHits; ++$n) {
                            $hit = new StatHit();
                            $hit->setUserKey($key);
                            $hit->setBrowserName(
                                $faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Internet Explorer'])
                            );
                            $hit->setOsName($faker->randomElement(['Mac OS X', 'Windows', 'Linux']));
                            $hit->setUrl(
                                'https://'.$faker->randomElement(['facebook', 'skype', 'google', 'lycos', 'caramail']).
                                '.com'
                            );
                            $hit->setBroadcast($broadcast);
                            $manager->persist($hit);
                        }
                    }
                }
            }

            $manager->persist($campaign);
            $this->addReference("campaign-{$i}", $campaign);
        }
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            MailingListFixtures::class,
        ];
    }
}
