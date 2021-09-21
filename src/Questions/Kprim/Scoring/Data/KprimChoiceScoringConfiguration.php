<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Kprim\Scoring\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceScoringConfiguration extends AbstractValueObject
{
    protected ?float $points;

    protected ?int $half_points_at;

    public function __construct(?float $points = null, ?int $half_points_at = null)
    {
        $this->points = $points;
        $this->half_points_at = $half_points_at;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }

    public function getHalfPointsAt() : ?int
    {
        return $this->half_points_at;
    }
}
