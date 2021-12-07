<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Scoring\Data;

/**
 * Class EssayScoringProcessedAnswerOption
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayScoringProcessedAnswerOption
{
    /**
     * @var string[]
     */
    private array $words;

    private ?float $points;

    public function __construct(EssayScoringDefinition $def, bool $is_case_insensitive)
    {
        $this->points = $def->getPoints();

        $text = $def->getText();

        if ($is_case_insensitive) {
            $text = strtoupper($text);
        }

        // ignore punctuation
        $this->words = explode(' ', preg_replace("#[[:punct:]]#", "", $text));
    }

    /**
     * @return string[]
     */
    public function getWords() : array
    {
        return $this->words;
    }

    public function getPoints() : float
    {
        return $this->points;
    }
}
