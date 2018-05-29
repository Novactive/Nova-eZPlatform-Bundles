<?php
/**
 * @copyright Novactive
 * Date: 5/29/18
 */

namespace MC\Bundle\LDAPBundle;

use MC\Bundle\PrivateContentAccessBundle\DependencyInjection\MCPrivateContentAccessBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MCLDAPBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new MCLDAPBundleExtension(__DIR__.'/Resources/config');
        }

        return $this->extension;
    }
}
