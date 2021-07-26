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
    protected ?float $points;

    protected ?string $text;

    public function __construct(?float $points = null, ?string $text = null)
    {
        $this->points = $points;
        $this->text = $text;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }

    public function getText() : ?string
    {
        return $this->text;
    }
}
