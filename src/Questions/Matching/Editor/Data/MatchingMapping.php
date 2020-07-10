<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MatchingMapping
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingMapping extends AbstractValueObject
{
    /**
     * @var ?string
     */
    protected $definition_id;

    /**
     * @var ?string
     */
    protected $term_id;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param string $definition_id
     * @param string $term_id
     * @param float $points
     * @return MatchingMapping
     */
    public static function create(?string $definition_id, ?string $term_id, ?float $points) : MatchingMapping
    {
        $object = new MatchingMapping();
        $object->definition_id = $definition_id;
        $object->term_id = $term_id;
        $object->points = $points;
        return $object;
    }

    /**
     * @return ?string
     */
    public function getDefinitionId() : ?string
    {
        return $this->definition_id;
    }

    /**
     * @return ?string
     */
    public function getTermId() : ?string
    {
        return $this->term_id;
    }

    /**
     * @return ?float
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }
}
