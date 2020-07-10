<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceScoringDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceScoringDefinition extends AbstractValueObject
{
    /**
     * @var bool
     */
    protected $correct_value;

    /**
     * @param bool $correct_value
     * @return KprimChoiceScoringDefinition
     */
    public static function create(bool $correct_value) : KprimChoiceScoringDefinition
    {
        $object = new KprimChoiceScoringDefinition();
        $object->correct_value = $correct_value;
        return $object;
    }

    /**
     * @return boolean
     */
    public function isCorrectValue() : bool
    {
        return $this->correct_value;
    }
}
