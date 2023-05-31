<?php

namespace Novactive\EzRssFeedBundle\Form\Transformer;

use Novactive\EzRssFeedBundle\Entity\RssFeedSite;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class MultipleChoicesTransformer implements DataTransformerInterface
{
    public function transform($rssFeedSites): array
    {
        // Transform the collection of choices to an array of values
        $choices = [];
        foreach ($rssFeedSites as $rssFeedSite) {
            if ($rssFeedSite instanceof RssFeedSite) {
                $choices[$rssFeedSite->getIdentifier()] = $rssFeedSite->getIdentifier();
            }
        }

        return $choices;
    }

    public function reverseTransform($choices): ArrayCollection
    {
        // Transform the array of values to a collection of choices
        $rssFeedSites = new ArrayCollection();
        if (is_array($choices)) {
            foreach ($choices as $siteIdentifier) {
                $rssFeedSite = new RssFeedSite();
                $rssFeedSite->setIdentifier($siteIdentifier);
                $rssFeedSites->add($rssFeedSite);
            }

        }

        return $rssFeedSites;
    }
}