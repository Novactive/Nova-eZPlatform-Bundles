<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Responder;

use eZ\Publish\API\Repository\Repository;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Message as MessageConverter;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Query as QueryConverter;
use Novactive\Bundle\eZSlackBundle\Core\Event\Searched;
use Novactive\Bundle\eZSlackBundle\Core\Event\Selected;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;

class Search extends Responder
{
    private Repository $repository;

    private QueryConverter $queryConverter;

    private MessageConverter $messageConverter;

    public function __construct(
        Repository $repository,
        QueryConverter $queryConverter,
        MessageConverter $messageConverter
    ) {
        $this->repository = $repository;
        $this->queryConverter = $queryConverter;
        $this->messageConverter = $messageConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'Search in the eZ Content Repository.';
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp(): string
    {
        return 'You can search complex query like: (banana OR apple) AND plop +dietcoke AND contentType:article';
    }

    /**
     * {@inheritdoc}
     */
    public function respond(array $arguments = []): Message
    {
        $eZQuery = $this->queryConverter->convert(implode(' ', $arguments));
        $results = $this->repository->getSearchService()->findContent($eZQuery);

        $message = new Message();
        if (0 === $results->totalCount) {
            $message->setText('Sorry, there is no result for this search.');
        } elseif (1 === $results->totalCount) {
            $message->setText('Only one selected for that search!');
            $message = $this->messageConverter->convert(
                new Selected($results->searchHits[0]->valueObject->id),
                $message
            );
        } else {
            $message->setText(sprintf('I found %d contents.', $results->totalCount));
            foreach ($results->searchHits as $searchHit) {
                $signal = new Searched($searchHit->valueObject->id);
                $contentMessage = $this->messageConverter->convert($signal);
                // we just want the main
                $message->addAttachment($contentMessage->getAttachments()[0]);
            }
        }

        return $message;
    }
}
