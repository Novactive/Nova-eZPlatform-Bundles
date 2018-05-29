<?php
/**
 * @copyright Novactive
 * Date: 5/29/18
 */

namespace Novactive\EzLdapAuthenticatorBundle;

use MC\Bundle\PrivateContentAccessBundle\DependencyInjection\MCPrivateContentAccessBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NovaEzLdapAuthenticatorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new NovaEzLdapAuthenticatorBundleExtension(__DIR__.'/Resources/config');
        }
        return $this->extension;
    }
}
