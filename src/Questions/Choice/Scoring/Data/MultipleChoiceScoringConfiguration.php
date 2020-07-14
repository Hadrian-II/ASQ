<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringConfiguration extends AbstractValueObject
{
    public static function create() : MultipleChoiceScoringConfiguration
    {
        return new MultipleChoiceScoringConfiguration();
    }
}
