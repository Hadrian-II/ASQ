<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Scoring;

use Exception;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Essay\EssayAnswer;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringConfiguration;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringDefinition;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringProcessedAnswerOption;

/**
 * Class EssayScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoring extends AbstractScoring
{
    const SCORING_MANUAL = 1;
    const SCORING_AUTOMATIC_ANY = 2;
    const SCORING_AUTOMATIC_ALL = 3;
    const SCORING_AUTOMATIC_ONE = 4;

    /**
     * @var EssayScoringConfiguration
     */
    protected $configuration;

    /**
     * @var string[]
     */
    private $words;

    /**
     * @var EssayScoringProcessedAnswerOption[]
     */
    private $answer_options;

    /**
     * @param QuestionDto $question
     */
    public function __construct($question)
    {
        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    public function score(Answer $answer) : float
    {
        if ($this->configuration->getScoringMode() === self::SCORING_MANUAL) {
            // TODO handle manual scoring
            throw new Exception("Dont run score on manual scoring");
        } else {
            $reached_points = $this->generateScore($answer->getText());

            return $reached_points;
        }
    }

    /**
     * @param string $text
     * @return float
     */
    private function generateScore(string $text) : float
    {
        $text = strip_tags($text);

        if ($this->configuration->getMatchingMode() === TextScoring::TM_CASE_INSENSITIVE) {
            $text = strtoupper($text);
        }

        //ignore punctuation
        $this->words = explode(' ', preg_replace("#[[:punct:]]#", "", $text));

        $this->answer_options = array_map(function ($definition) {
            return new EssayScoringProcessedAnswerOption($definition, $this->configuration->getMatchingMode() === TextScoring::TM_CASE_INSENSITIVE);
        }, $this->configuration->getDefinitions());

        $points = 0;

        foreach ($this->answer_options as $answer_option) {
            $found = $this->textContainsOption($answer_option);

            // one match found
            if ($found && $this->configuration->getScoringMode() === self::SCORING_AUTOMATIC_ONE) {
                return $this->configuration->getPoints();
            }

            // one error found
            if (!$found && $this->configuration->getScoringMode() === self::SCORING_AUTOMATIC_ALL) {
                return 0;
            }

            // match found
            if ($found && $this->configuration->getScoringMode() === self::SCORING_AUTOMATIC_ANY) {
                $points += $answer_option->getPoints();
            }
        }

        switch ($this->configuration->getScoringMode()) {
            case self::SCORING_AUTOMATIC_ALL:
                // all matches found
                return $this->configuration->getPoints();
            case self::SCORING_AUTOMATIC_ANY:
                return $points;
            case self::SCORING_AUTOMATIC_ONE:
                // no match found
                return 0;
        }
    }

    /**
     * @param EssayScoringProcessedAnswerOption $answer_option
     * @return bool
     */
    private function textContainsOption(EssayScoringProcessedAnswerOption $answer_option) : bool
    {
        $answer_words = $answer_option->getWords();

        switch ($this->configuration->getMatchingMode()) {
            case TextScoring::TM_LEVENSHTEIN_1:
                $max_distance = 1;
                break;
            case TextScoring::TM_LEVENSHTEIN_2:
                $max_distance = 2;
                break;
            case TextScoring::TM_LEVENSHTEIN_3:
                $max_distance = 3;
                break;
            case TextScoring::TM_LEVENSHTEIN_4:
                $max_distance = 4;
                break;
            case TextScoring::TM_LEVENSHTEIN_5:
                $max_distance = 5;
                break;
            default:
                $max_distance = 0;
                break;
        }

        for ($i = 0; $i < (count($this->words) - (count($answer_words) - 1)); $i++) {
            $distance = 0;

            for ($j = 0; $j < count($answer_words); $j++) {
                $distance += levenshtein($this->words[$i + $j], $answer_words[$j]);

                if ($distance > $max_distance) {
                    break;
                }
            }

            if ($distance <= $max_distance) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        if ($this->configuration->getScoringMode() === self::SCORING_AUTOMATIC_ANY) {
            return array_sum(
                array_map(
                            function ($definition) {
                                return $definition->getPoints();
                            },
                            $this->configuration->getDefinitions()
                        )
            );
        } else {
            return $this->configuration->getPoints();
        }
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::getBestAnswer()
     */
    public function getBestAnswer() : Answer
    {
        $text = implode(' ', array_map(function ($definition) {
            return $definition->getText();
        }, $this->configuration->getDefinitions()));

        return EssayAnswer::create($text);
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (empty($this->configuration->getScoringMode())) {
            return false;
        }

        if ($this->configuration->getScoringMode() !== self::SCORING_MANUAL) {
            foreach ($this->configuration->getDefinitions() as $definition) {
                /** @var EssayScoringDefinition $option_config */

                if (empty($definition->getText()) ||
                    ($this->configuration->getScoringMode() === self::SCORING_AUTOMATIC_ANY && empty($definition->getPoints()))) {
                    return false;
                }
            }
        }

        return true;
    }
}
