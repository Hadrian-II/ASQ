<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Scoring;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;

/**
 * Abstract Class AbstractScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractScoring
{
    const ANSWER_CORRECT = 1;
    const ANSWER_INCORRECT = 2;
    const ANSWER_CORRECTNESS_NOT_DETERMINABLLE = 3;

    const SCORING_DEFINITION_SUFFIX = 'Definition';

    /**
     * @var QuestionDto
     */
    protected $question;

    /**
     * @var float
     */
    protected $max_score;

    /**
     * @var float
     */
    protected $min_score;

    /**
     * AbstractScoring constructor.
     *
     * @param QuestionDto $question
     * @param array       $configuration
     */
    public function __construct(QuestionDto $question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public static function getScoringDefinitionClass() : string
    {
        return get_called_class() . self::SCORING_DEFINITION_SUFFIX;
    }


    public abstract function isComplete() : bool;

    /**
     * @param Answer $answer
     * @return float
     */
    abstract function score(Answer $answer) : float;

    /**
     * @return float
     */
    public function getMaxScore() : float {
        if (is_null($this->max_score)) {
            $this->max_score = $this->calculateMaxScore();
        }

        return $this->max_score;
    }

    /**
     * @return float
     */
    protected abstract function calculateMaxScore() : float;

    /**
     * @return float
     */
    public function getMinScore() : float {
        if (is_null($this->max_score)) {
            $this->max_score = $this->calculateMinScore();
        }

        return $this->max_score;
    }

    /**
     * @return float
     */
    protected function calculateMinScore() : float
    {
        return $this->calculateMaxHintDeduction();
    }

    /**
     * @return float
     */
    protected function calculateMaxHintDeduction() : float {
        if ($this->question->hasHints()) {
            return array_reduce($this->question->getQuestionHints()->getHints(), function($sum, $hint) {
                return $sum += $hint->getPointDeduction();
            }, 0.0);
        } else {
            return 0.0;
        }
    }

    /**
     * @param float $reached_points
     * @param float $max_points
     *
     * @return int
     */
    public function getAnswerFeedbackType(float $reached_points) : int
    {
        if ($this->getMaxScore() < PHP_FLOAT_EPSILON) {
            return self::ANSWER_CORRECTNESS_NOT_DETERMINABLLE;
        }
        else if (abs($reached_points - $this->getMaxScore()) < PHP_FLOAT_EPSILON) {
            return self::ANSWER_CORRECT;
        }
        else {
            return self::ANSWER_INCORRECT;
        }
    }

    public abstract function getBestAnswer() : Answer;
}