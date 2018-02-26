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
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;

/**
 * Class MailingListFixtures.
 */
class MailingListFixtures extends Fixture
{
    const FIXTURE_COUNT_MAILINGLIST = 10;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 1; $i <= self::FIXTURE_COUNT_MAILINGLIST; ++$i) {
            $mailingList = new MailingList();
            $mailingList->setNames(
                [
                    'fre-FR' => $faker->unique()->sentence(6).'( FR )',
                    'eng-GB' => $faker->unique()->sentence(6).'( GB )',
                    'eng-US' => $faker->unique()->sentence(6).'( US )',
                ]
            );
            $manager->persist($mailingList);
            $this->addReference("mailing-list-{$i}", $mailingList);
        }
        $manager->flush();
    }
}
