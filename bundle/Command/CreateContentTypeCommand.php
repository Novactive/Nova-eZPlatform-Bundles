<?php

namespace Novactive\Bundle\NovaeZEditHelpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Novactive\Bundle\NovaeZEditHelpBundle\Services\FetchDocumentation;


class CreateContentTypeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('novahelptooltip:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $repository \eZ\Publish\API\Repository\Repository */
        $repository = $this->getContainer()->get('ezpublish.api.repository');
        $repository->setCurrentUser($repository->getUserService()->loadUser(14));

        $contentTypeService = $repository->getContentTypeService();
        $contentTypeIdentifier = FetchDocumentation::TOOLTIP_CONTENT_TYPE;

        try {
            $contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

            $output->writeln(sprintf('<error>Content Type with identifier %s already exists</error>', $contentTypeIdentifier));
        } catch (\eZ\Publish\API\Repository\Exceptions\NotFoundException $e) {

            try {
                $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
            } catch (\eZ\Publish\API\Repository\Exceptions\NotFoundException $e) {
                $output->writeln('<error>Content Type not found with identifier Content</error>');

                return;
            }

            // instantiate a ContentTypeCreateStruct with the given content type identifier and set parameters
            $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct($contentTypeIdentifier);
            $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
            // We set the Content Type naming pattern to the title's value
            $contentTypeCreateStruct->nameSchema = '<title>';

            // set names for the content type
            $contentTypeCreateStruct->names = [
                'eng-GB' => 'Nova Help Tooltip'
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
                $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
                $contentTypeService->publishContentTypeDraft($contentTypeDraft);
                $output->writeln(sprintf('<info>Content type created %s with ID %s</info>', $contentTypeIdentifier, $contentTypeDraft->id));
            } catch (\eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            } catch (\eZ\Publish\API\Repository\Exceptions\ForbiddenException $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }

        }

    }
}