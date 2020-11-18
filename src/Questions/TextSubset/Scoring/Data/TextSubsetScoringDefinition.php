<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Scoring\Data;

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
     * @param float $points
     * @param string $text
     */
    public function __construct(?float $points = null, ?string $text = null)
    {
        $this->points = $points;
        $this->text = $text;
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
