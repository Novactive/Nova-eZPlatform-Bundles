<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzMenuManagerBundle\DependencyInjection\Security\Provider;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;
use JMS\TranslationBundle\Model\Message;

class MenuPolicyProvider extends YamlPolicyProvider
{
    public function getFiles(): array
    {
        return [
            __DIR__.'/../../../Resources/config/policies.yml',
        ];
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.menu_manager.all_functions', 'menu_manager'))
                ->setDesc('Menu Manager / All Functions'),
            (new Message('role.policy.menu_manager.list', 'menu_manager'))->setDesc('Menu Manager / Liste'),
            (new Message('role.policy.menu_manager.new', 'menu_manager'))->setDesc('Menu Manager / New'),
            (new Message('role.policy.menu_manager.edit', 'menu_manager'))->setDesc('Menu Manager / Edit'),
            (new Message('role.policy.menu_manager.delete', 'menu_manager'))->setDesc('Menu Manager / Delete'),
            (new Message('role.policy.menu_manager.view', 'menu_manager'))->setDesc('Menu Manager Log / View'),
        ];
    }
}
