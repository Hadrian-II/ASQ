<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetScoringDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringDefinition extends AbstractValueObject
{
    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var ?string
     */
    protected $text;

    /**
     * TextSubsetScoringDefinition constructor.
     * @param int $points
     */
    public static function create(?float $points = null, ?string $text = null) : TextSubsetScoringDefinition
    {
        $object = new TextSubsetScoringDefinition();
        $object->points = $points;
        $object->text = $text;
        return $object;
    }

    /**
     * @return int
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return string
     */
    public function getText() : ?string
    {
        return $this->text;
    }
}