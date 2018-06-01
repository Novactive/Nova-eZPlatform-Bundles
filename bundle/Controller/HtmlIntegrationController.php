<?php
/**
 * NovaHtmlIntegrationBundle.
 *
 * @package   Novactive\Bundle\HtmlIntegrationBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaHtmlIntegrationBundle/blob/master/LICENSE
 */

namespace Novactive\Bundle\HtmlIntegrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HtmlIntegrationController extends Controller
{
    /**
     * @param string $template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($template = 'index')
    {
        try {
            return $this->render("@ezdesign/{$template}.html.twig");
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException();
        }
    }
}
