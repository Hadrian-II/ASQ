<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingEditorConfiguration extends AbstractValueObject
{
    const SHUFFLE_NONE = 0;
    const SHUFFLE_DEFINITIONS = 1;
    const SHUFFLE_TERMS = 2;
    const SHUFFLE_BOTH = 3;

    protected ?int $shuffle;

    protected ?int $thumbnail_size;

    const MATCHING_ONE_TO_ONE = 0;
    const MATCHING_MANY_TO_ONE = 1;
    const MATCHING_MANY_TO_MANY = 2;

    protected ?int $matching_mode;

    /**
     * @var ?MatchingItem[]
     */
    protected ?array $definitions;

    /**
     * @var ?MatchingItem[]
     */
    protected ?array $terms;

    /**
     * @var ?MatchingMapping[]
     */
    protected ?array $matches;

    /**
     * @param ?int $shuffle
     * @param ?int $thumbnail_size
     * @param ?int $matching_mode
     * @param ?MatchingItem[] $definitions
     * @param ?MatchingItem[] $terms
     * @param ?MatchingItem[] $matches
     */
    public function __construct(
        ?int $shuffle = self::SHUFFLE_NONE,
        ?int $thumbnail_size = 100,
        ?int $matching_mode = self::MATCHING_ONE_TO_ONE,
        ?array $definitions = [],
        ?array $terms = [],
        ?array $matches = []
    ) {
        $this->shuffle = $shuffle;
        $this->thumbnail_size = $thumbnail_size;
        $this->matching_mode = $matching_mode;
        $this->definitions = $definitions;
        $this->terms = $terms;
        $this->matches = $matches;
    }

    public function getShuffle() : ?int
    {
        return $this->shuffle;
    }

    public function getThumbnailSize() : ?int
    {
        return $this->thumbnail_size;
    }

    public function getMatchingMode() : ?int
    {
        return $this->matching_mode;
    }

    /**
     * @return ?MatchingItem[]
     */
    public function getDefinitions() : ?array
    {
        if ($this->isShuffleDefinitions()) {
            shuffle($this->definitions);
        }

        return $this->definitions;
    }

    private function isShuffleDefinitions() : bool
    {
        return $this->shuffle === self::SHUFFLE_DEFINITIONS ||
        $this->shuffle === self::SHUFFLE_BOTH;
    }

    /**
     * @return ?MatchingItem[]
     */
    public function getTerms() : ?array
    {
        if ($this->isShuffleTerms()) {
            shuffle($this->terms);
        }

        return $this->terms;
    }

    private function isShuffleTerms() : bool
    {
        return $this->shuffle === self::SHUFFLE_TERMS ||
        $this->shuffle === self::SHUFFLE_BOTH;
    }

    /**
     * @return ?MatchingMapping
     */
    public function getMatches() : ?array
    {
        return $this->matches;
    }
}
