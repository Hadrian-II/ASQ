<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Essay\Scoring\EssayScoring;

/**
 * Class EssayScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayScoringConfiguration extends AbstractValueObject
{
    protected ?int $matching_mode;

    protected ?int $scoring_mode;

    protected ?float $points;

    public function __construct(
        ?int $matching_mode = TextScoring::TM_CASE_INSENSITIVE,
        ?int $scoring_mode = EssayScoring::SCORING_MANUAL,
        ?float $points = null
    ) {
        $this->matching_mode = $matching_mode;
        $this->scoring_mode = $scoring_mode;
        $this->points = $points;
    }

    public function getMatchingMode() : ?int
    {
        return $this->matching_mode;
    }

    public function getScoringMode() : ?int
    {
        return $this->scoring_mode;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }
}
