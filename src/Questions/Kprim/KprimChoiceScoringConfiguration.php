<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Kprim;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class KprimChoiceScoringConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceScoringConfiguration extends AbstractConfiguration
{

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var ?int
     */
    protected $half_points_at;

    /**
     * @param ?float $points
     * @param ?int $half_points_at
     * @return KprimChoiceScoringConfiguration
     */
    static function create(?float $points = null, ?int $half_points_at = null) : KprimChoiceScoringConfiguration
    {
        $object = new KprimChoiceScoringConfiguration();
        $object->points = $points;
        $object->half_points_at = $half_points_at;
        return $object;
    }

    /**
     * @return ?int
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return ?int
     */
    public function getHalfPointsAt() : ?int
    {
        return $this->half_points_at;
    }
}