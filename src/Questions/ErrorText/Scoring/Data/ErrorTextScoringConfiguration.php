<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText\Scoring\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ErrorTextScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ErrorTextScoringConfiguration extends AbstractValueObject
{
    protected ?float $points_wrong;

    public function __construct(?float $points_wrong = null)
    {
        $this->points_wrong = $points_wrong;
    }

    public function getPointsWrong() : ?float
    {
        return $this->points_wrong;
    }
}
