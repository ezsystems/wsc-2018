<?php

namespace EzSystems\PollBundle\Security\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\SearchService;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;
use EzSystems\RepositoryForms\Limitation\Mapper\MultipleSelectionBasedMapper;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

class QuestionLimitationFormMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    use LoggerAwareTrait;

    /** @var \eZ\Publish\Core\Repository\SearchService */
    private $searchService;

    /** @var \eZ\Publish\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\Core\Repository\ContentService */
    private $contentService;

    public function __construct(
        SearchService $searchService,
        ContentTypeService $contentTypeService,
        ContentService $contentService
    )
    {
        $this->searchService = $searchService;
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->logger = new NullLogger();
    }

    protected function getSelectionChoices()
    {
        $contentTypes = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            /** @var \eZ\Publish\Core\Repository\Values\ContentType\ContentType $contentType */
            foreach ($this->contentTypeService->loadContentTypes($group) as $contentType) {
                foreach ($contentType->fieldDefinitions as $fieldDefinition ){
                    if ($fieldDefinition->fieldTypeIdentifier === 'ezpoll'){
                        $contentTypes[$contentType->identifier] = $contentType->identifier;
                    }
                }
            }
        }
        $res = [];
        foreach ($contentTypes as $contentTypeIdentifier){
            $queryProperties = array(
                'filter' => new Criterion\ContentTypeIdentifier($contentTypeIdentifier),
            );

            $searchResult = $this->searchService->findContent(
                new Query($queryProperties)
            );

            $res[] = $searchResult->searchHits;
        }

        foreach (array_merge(...$res) as $searchHit) {

            /** @var \eZ\Publish\Core\Repository\Values\Content\Content $content */
            $content = $searchHit->valueObject;
            $a[$content->id] = $content->getName();
        }

        return $a;
    }

    /**
     * Map the limitation values, in order to pass them as context of limitation value rendering.
     *
     * @param Limitation $limitation
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        $values = [];
        foreach ($limitation->limitationValues as $contentId) {
            try {
                $values[] = $this->contentService->loadContent($contentId)->getName();
            } catch (NotFoundException $e) {
                $this->logger->error(sprintf('Could not map limitation value: Content Type with id = %s not found', $contentTypeId));
            }
        }

        return $values;
    }
}