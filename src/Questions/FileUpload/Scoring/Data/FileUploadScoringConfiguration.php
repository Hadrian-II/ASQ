<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FileUploadScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadScoringConfiguration extends AbstractValueObject
{
    protected ?float $points;

    protected ?bool $completed_by_submition;

    public function __construct(?float $points = null, ?bool $completed_by_submition = null)
    {
        $this->points = $points;
        $this->completed_by_submition = $completed_by_submition;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }

    public function isCompletedBySubmition() : ?bool
    {
        return $this->completed_by_submition;
    }
}
