<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace AppBundle\Tab\Dashboard;

use Pagerfanta\Pagerfanta;

class ContentToPollDataMapper
{
    /**
     * @param Pagerfanta $pager
     *
     * @return array
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];
        /** @var \eZ\Publish\Core\Repository\Values\Content\Content $content */
        foreach ($pager as $content) {
            foreach ($content->getFields() as $field) {
                if ($field->fieldTypeIdentifier === 'ezpoll') {
                    $contentInfo = $content->versionInfo->contentInfo;
                    $data[] = [
                        'contentId' => $content->id,
                        'name' => $contentInfo->name,
                        'language' => $contentInfo->mainLanguageCode,
                        'version' => $content->versionInfo->versionNo,
                        'modified' => $content->versionInfo->modificationDate,
                        'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
                        'question' => $field->value->question,
                        'answers' => $field->value->answers,
                    ];
                }
            }
        }

        return $data;
    }
}
