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
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringConfiguration extends AbstractValueObject
{
    /**
     * @var ?int
     */
    protected $matching_mode;

    /**
     * @var ?int
     */
    protected $scoring_mode;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var ?EssayScoringDefinition[]
     */
    protected $definitions;

    /**
     * @param int $matching_mode
     * @param int $scoring_mode
     * @param float $points
     * @return EssayScoringConfiguration
     */
    public static function create(
        ?int $matching_mode = TextScoring::TM_CASE_INSENSITIVE,
        ?int $scoring_mode = EssayScoring::SCORING_MANUAL,
        ?float $points = null,
        ?array $definitions = null
    ) : EssayScoringConfiguration {
        $object = new EssayScoringConfiguration();

        $object->matching_mode = $matching_mode;
        $object->scoring_mode = $scoring_mode;
        $object->points = $points;
        $object->definitions = $definitions;

        return $object;
    }

    /**
     * @return ?int
     */
    public function getMatchingMode() : ?int
    {
        return $this->matching_mode;
    }

    /**
     * @return ?int
     */
    public function getScoringMode() : ?int
    {
        return $this->scoring_mode;
    }

    /**
     * @return ?float
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return ?EssayScoringDefinition[]
     */
    public function getDefinitions() : ?array
    {
        return $this->definitions;
    }
}
