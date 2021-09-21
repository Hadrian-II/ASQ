<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric\Scoring\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class NumericScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class NumericScoringConfiguration extends AbstractValueObject
{
    protected ?float $points;

    protected ?float $lower_bound;

    protected ?float $upper_bound;

    public function __construct(
        ?float $points = null,
        ?float $lower_bound = null,
        ?float $upper_bound = null
    ) {
        $this->points = $points;
        $this->lower_bound = $lower_bound;
        $this->upper_bound = $upper_bound;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }

    public function getLowerBound() : ?float
    {
        return $this->lower_bound;
    }

    public function getUpperBound() : ?float
    {
        return $this->upper_bound;
    }
}
