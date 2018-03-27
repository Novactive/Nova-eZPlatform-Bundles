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
$loader = include __DIR__.'/autoload_orig.php';
$loader->addPsr4('Novactive\\Bundle\\eZMailingBundle\\', __DIR__.'/../src/Novactive/Bundle/eZMailingBundle');
