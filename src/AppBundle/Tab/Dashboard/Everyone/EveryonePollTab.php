<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace AppBundle\Tab\Dashboard\Everyone;

use AppBundle\Tab\Dashboard\ContentToPollDataMapper;
use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use eZ\Publish\Core\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class EveryonePollTab extends AbstractTab implements OrderedTabInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\Core\Repository\SearchService */
    private $searchService;

    /** @var \AppBundle\Tab\Dashboard\ContentToPollDataMapper */
    private $contentToPollDataMapper;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\Core\Repository\SearchService $searchService
     * @param \AppBundle\Tab\Dashboard\ContentToPollDataMapper $contentToPollDataMapper
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        ContentToPollDataMapper $contentToPollDataMapper
    )
    {
        parent::__construct($twig, $translator);

        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->contentToPollDataMapper = $contentToPollDataMapper;
    }

    /**
     * Get the order of this tab.
     *
     * @return int
     */
    public function getOrder(): int
    {
        return 300;
    }

    /**
     * Returns identifier of the tab.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'everyone-poll';
    }

    /**
     * Returns name of the tab which is displayed as a tab's title in the UI.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Polls';
    }

    /**
     * Returns HTML body of the tab.
     *
     * @param array $parameters
     *
     * @return string
     */
    public function renderView(array $parameters): string
    {
        $page = 1;
        $limit = 10;

        $pager = new Pagerfanta(
            new ArrayAdapter($this->getAllContentContainingPoll())
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('@App/dashboard/tab/all_poll.html.twig', [
            'data' => $this->contentToPollDataMapper->map($pager),
        ]);
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content[]
     */
    private function getAllContentContainingPoll(): array
    {
        $contentTypes = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            /** @var \eZ\Publish\Core\Repository\Values\ContentType\ContentType $contentType */
            foreach ($this->contentTypeService->loadContentTypes($group) as $contentType) {
                foreach ($contentType->fieldDefinitions as $fieldDefinition ) {
                    if ($fieldDefinition->fieldTypeIdentifier === 'ezpoll') {
                        $contentTypes[$contentType->identifier] = $contentType->identifier;
                    }
                }
            }
        }

        $searchHits = [];
        foreach ($contentTypes as $contentTypeIdentifier) {
            $queryProperties = array(
                'filter' => new Criterion\ContentTypeIdentifier($contentTypeIdentifier),
            );

            $searchResult = $this->searchService->findContent(
                new Query($queryProperties)
            );

            $searchHits[] = $searchResult->searchHits;
        }

        if (empty($searchHits)) {
            return [];
        }

        $contents = [];
        foreach (array_merge(...$searchHits) as $searchHit) {
            $contents[] = $searchHit->valueObject;
        }

        return $contents;
    }
}
