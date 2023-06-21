<?php

namespace Novactive\EzRssFeedBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Novactive\EzRssFeedBundle\Entity\RssFeedSite;
use Symfony\Component\Form\DataTransformerInterface;

class MultipleChoicesTransformer implements DataTransformerInterface
{
    public function transform($value): array
    {
        // Transform the collection of choices to an array of values
        $choices = [];
        foreach ($value as $rssFeedSite) {
            if ($rssFeedSite instanceof RssFeedSite) {
                $choices[$rssFeedSite->getIdentifier()] = $rssFeedSite->getIdentifier();
            }
        }

        return $choices;
    }

    public function reverseTransform($value): ArrayCollection
    {
        // Transform the array of values to a collection of choices
        $rssFeedSites = new ArrayCollection();
        if (is_array($value)) {
            foreach ($value as $siteIdentifier) {
                $rssFeedSite = new RssFeedSite();
                $rssFeedSite->setIdentifier($siteIdentifier);
                $rssFeedSites->add($rssFeedSite);
            }
        }

        return $rssFeedSites;
    }
}
