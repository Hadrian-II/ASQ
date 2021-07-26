<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceScoringDefinition extends AbstractValueObject
{
    protected ?float $points_selected;

    protected ?float $points_unselected;

    public function __construct(
        ?float $points_selected = null,
        ?float $points_unselected = null)
    {
        $this->points_selected = $points_selected;
        $this->points_unselected = $points_unselected;
    }

    public function getPointsSelected() : ?float
    {
        return $this->points_selected;
    }

    public function getPointsUnselected() : ?float
    {
        return $this->points_unselected;
    }
}
