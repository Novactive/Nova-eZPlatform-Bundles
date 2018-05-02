<?php
/**
 * @copyright Novactive
 * Date: 3/16/18
 */

namespace MC\Bundle\PrivateContentAccessBundle;

use MC\Bundle\PrivateContentAccessBundle\DependencyInjection\MCPrivateContentAccessBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MCPrivateContentAccessBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new MCPrivateContentAccessBundleExtension(__DIR__.'/Resources/config');
        }

        return $this->extension;
    }
}
