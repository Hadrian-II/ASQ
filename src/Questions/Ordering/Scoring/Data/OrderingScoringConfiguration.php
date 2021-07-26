<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class OrderingScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class OrderingScoringConfiguration extends AbstractValueObject
{
    protected ?float $points;

    public function __construct(?float $points = null)
    {
        $this->points = $points;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }
}
