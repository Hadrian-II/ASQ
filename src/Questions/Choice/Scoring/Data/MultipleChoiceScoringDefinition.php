<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceScoringDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringDefinition extends AbstractValueObject
{
    /**
     * @var ?float
     */
    protected $points_selected;

    /**
     * @var ?float
     */
    protected $points_unselected;

    /**
     * @param float $points_selected
     * @param float $points_unselected
     */
    public function __construct(
        ?float $points_selected = null,
        ?float $points_unselected = null)
    {
        $this->points_selected = $points_selected;
        $this->points_unselected = $points_unselected;
    }

    /**
     * @return int
     */
    public function getPointsSelected() : ?float
    {
        return $this->points_selected;
    }

    /**
     * @return int
     */
    public function getPointsUnselected() : ?float
    {
        return $this->points_unselected;
    }
}
