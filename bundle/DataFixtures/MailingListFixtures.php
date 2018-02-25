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
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;

class MailingListFixtures extends Fixture
{
    const FIXTURE_COUNT_MAILINGLIST = 10;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= self::FIXTURE_COUNT_MAILINGLIST; ++$i) {
            $mailingList = new MailingList();
            $mailingList->setNames(
                [
                    'fre-FR' => "Ma List {$i}",
                    'eng-GB' => "My GB List {$i}",
                    'eng-US' => "My US List {$i}",
                ]
            );
            $manager->persist($mailingList);
            $this->addReference("mailing-list-{$i}", $mailingList);
        }
        $manager->flush();
    }
}
