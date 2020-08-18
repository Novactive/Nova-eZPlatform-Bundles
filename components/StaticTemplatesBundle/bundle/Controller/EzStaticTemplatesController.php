<?php

/**
 * NovaeZStaticTemplatesBundle.
 *
 * @package   Novactive\Bundle\EzStaticTemplatesBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZStaticTemplatesBundle/blob/master/LICENSE
 */

namespace Novactive\Bundle\EzStaticTemplatesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EzStaticTemplatesController extends Controller
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
