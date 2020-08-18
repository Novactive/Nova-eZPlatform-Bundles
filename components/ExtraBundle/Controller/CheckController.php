<?php

/**
 * NovaeZExtraBundle CheckController.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckController.
 */
class CheckController extends Controller
{
    /**
     * Test Route for the Bundle.
     *
     * @Route("/test")
     *
     * @return Response
     */
    public function testAction()
    {
        $response = new Response();
        $response->setContent('Novactive eZ Platform Extra Bundle');

        return $response;
    }
}
