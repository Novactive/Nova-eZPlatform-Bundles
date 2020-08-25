<?php

/**
 * NovaeZEditHelpBundle.
 *
 * @package   Novactive\Bundle\NovaeZEditHelpBundle
 *
 * @author    sergmike
 * @copyright 2019
 * @license   https://github.com/Novactive/NovaeZEditHelpBundle MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\NovaeZEditHelpBundle\Command;

use eZ\Publish\API\Repository\Exceptions\ForbiddenException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupCreateStruct;
use Novactive\Bundle\NovaeZEditHelpBundle\Services\FetchDocumentation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateContentTypeCommand extends Command
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @required
     */
    public function setDependencies(Repository $eZPublishRepository): void
    {
        $this->repository = $eZPublishRepository;
    }

    protected function configure(): void
    {
        $this->setName('novaezhelptooltip:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->repository;

        $contentTypeService = $repository->getContentTypeService();
        $contentTypeIdentifier = FetchDocumentation::TOOLTIP_CONTENT_TYPE;

        try {
            $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

            $output->writeln(
                sprintf('<error>Content Type with identifier %s already exists</error>', $contentTypeIdentifier)
            );
        } catch (NotFoundException $e) {
            $contentTypeGroupIdentifier = 'Help';

            try {
                $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier($contentTypeGroupIdentifier);
            } catch (NotFoundException $e) {
                $contentTypeGroupCreateStruct = new ContentTypeGroupCreateStruct();
                $contentTypeGroupCreateStruct->identifier = $contentTypeGroupIdentifier;
                $contentTypeGroup = $contentTypeService->createContentTypeGroup($contentTypeGroupCreateStruct);
                $output->writeln(
                    sprintf(
                        '<info>Content Type Group with name %s has been created</info>',
                        $contentTypeGroupIdentifier
                    )
                );
            }

            // instantiate a ContentTypeCreateStruct with the given content type identifier and set parameters
            $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct($contentTypeIdentifier);
            $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
            // We set the Content Type naming pattern to the title's value
            $contentTypeCreateStruct->nameSchema = '<title>';

            // set names for the content type
            $contentTypeCreateStruct->names = [
                'eng-GB' => 'Nova eZ Help Tooltip',
            ];

            $contentTypeCreateStruct->isContainer = true;

            // add a TextLine Field with identifier 'title'
            $titleFieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
            $titleFieldCreateStruct->names = ['eng-GB' => 'Title'];
            $titleFieldCreateStruct->fieldGroup = 'content';
            $titleFieldCreateStruct->position = 1;
            $titleFieldCreateStruct->isTranslatable = true;
            $titleFieldCreateStruct->isRequired = true;
            $titleFieldCreateStruct->isSearchable = true;
            $contentTypeCreateStruct->addFieldDefinition($titleFieldCreateStruct);

            // add a TextLine Field with identifier 'identifier'
            $identifierCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('identifier', 'ezstring');
            $identifierCreateStruct->names = ['eng-GB' => 'Identifier'];
            $identifierCreateStruct->fieldGroup = 'content';
            $identifierCreateStruct->position = 2;
            $identifierCreateStruct->isTranslatable = true;
            $identifierCreateStruct->isRequired = true;
            $identifierCreateStruct->isSearchable = true;
            $contentTypeCreateStruct->addFieldDefinition($identifierCreateStruct);

            // add a RichText Field body field
            $bodyFieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('body', 'ezrichtext');
            $bodyFieldCreateStruct->names = ['eng-GB' => 'Body'];
            $bodyFieldCreateStruct->fieldGroup = 'content';
            $bodyFieldCreateStruct->position = 3;
            $bodyFieldCreateStruct->isTranslatable = true;
            $bodyFieldCreateStruct->isRequired = false;
            $bodyFieldCreateStruct->isSearchable = true;
            $contentTypeCreateStruct->addFieldDefinition($bodyFieldCreateStruct);

            // add a Image Field
            $imageFieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('image', 'ezimage');
            $imageFieldCreateStruct->names = ['eng-GB' => 'Image'];
            $imageFieldCreateStruct->fieldGroup = 'content';
            $imageFieldCreateStruct->position = 4;
            $imageFieldCreateStruct->isTranslatable = true;
            $imageFieldCreateStruct->isRequired = false;
            $imageFieldCreateStruct->isSearchable = false;
            $contentTypeCreateStruct->addFieldDefinition($imageFieldCreateStruct);

            try {
                $contentTypeDraft = $contentTypeService->createContentType(
                    $contentTypeCreateStruct,
                    [$contentTypeGroup]
                );
                $contentTypeService->publishContentTypeDraft($contentTypeDraft);
                $output->writeln(
                    sprintf(
                        '<info>Content type created %s with ID %s</info>',
                        $contentTypeIdentifier,
                        $contentTypeDraft->id
                    )
                );
            } catch (UnauthorizedException $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            } catch (ForbiddenException $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
        }

        return Command::SUCCESS;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->repository->getPermissionResolver()->setCurrentUserReference(
            $this->repository->getUserService()->loadUser(14)
        );
    }
}
