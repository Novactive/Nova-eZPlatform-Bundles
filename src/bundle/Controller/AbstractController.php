<?php


namespace Novactive\EzRssFeedBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;

class AbstractController extends Controller
{
    public function getEntityManager()
    {
        return $this->getDoctrine()->getManager($this->getConfigResolver()->getParameter('repository'));
    }
}