<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MatchingMapping
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingMapping extends AbstractValueObject
{
    protected ?string $definition_id;

    protected ?string $term_id;

    protected ?float $points;

    public function __construct(
        ?string $definition_id = null,
        ?string $term_id = null,
        ?float $points = null
    ) {
        $this->definition_id = $definition_id;
        $this->term_id = $term_id;
        $this->points = $points;
    }

    public function getDefinitionId() : ?string
    {
        return $this->definition_id;
    }

    public function getTermId() : ?string
    {
        return $this->term_id;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }
}
