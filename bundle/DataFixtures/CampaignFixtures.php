<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/eZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;

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
                    'fre-FR' => "CAMPAIGN {$i}( FR )",
                    'eng-GB' => "CAMPAIGN {$i}( GB )",
                    'eng-US' => "CAMPAIGN {$i}( US )",
                ]
            );
            $campaign->setReportEmail($faker->email);
            $campaign->setSenderEmail($faker->email);
            $campaign->setSenderName($faker->name);
            $campaign->setContentId(1);
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
                        'fre-FR' => "MAILING {$k}( FR )",
                        'eng-GB' => "MAILING {$k}( GB )",
                        'eng-US' => "MAILING {$k}( US )",
                    ]
                );
                $mailing->setStatus($faker->randomElement(array_keys(Mailing::STATUSES)));
                $mailing->setRecurring($faker->boolean());
                $mailing->setHoursOfDay([$faker->numberBetween(0, 23)]);
                $mailing->setDaysOfMonth([$faker->numberBetween(0, 31)]);
                $mailing->setContentId(1);
                $campaign->addMailing($mailing);
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
