<?php
/**
 * File containing the LegacyConverter class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace AppBundle\eZ\Publish\FieldType\Poll;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class LegacyConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $storageFieldValue->dataText = json_encode($value->data);
        $storageFieldValue->sortKeyString = $value->sortKey;
    }
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = json_decode($value->dataText, true);
        $fieldValue->sortKey = $value->sortKeyString;
    }
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $storageDef->dataInt1 = $fieldDef->fieldTypeConstraints->validators['QuestionLengthValidator']['minStringLength'] ?? null;
        $storageDef->dataInt2 = $fieldDef->fieldTypeConstraints->validators['QuestionLengthValidator']['maxStringLength'] ?? null;
        $storageDef->dataInt3 = $fieldDef->fieldTypeConstraints->validators['AnswerListValueValidator']['answerLimit'] ?? null;
    }
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $minStringLength = $storageDef->dataInt1;
        $maxStringLength = $storageDef->dataInt2;
        $answerLimit = $storageDef->dataInt3;
            $fieldDef->fieldTypeConstraints->validators = [
                'QuestionLengthValidator' => [
                    'minStringLength' => $minStringLength,
                    'maxStringLength' => $maxStringLength
                ],
                'AnswerListValueValidator' => [
                    'answerLimit' => $answerLimit
                ],
            ];
    }
    /**
     * Returns the name of the index column in the attribute table
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * If the indexing is not supported, this method must return false.
     *
     * @return string|\eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\false
     */
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}
