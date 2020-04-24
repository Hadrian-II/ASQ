<?php
declare(strict_types=1);

namespace srag\asq\Questions\MultipleChoice;

use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class MultipleChoiceScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoring extends AbstractScoring
{

    function score(Answer $answer): float
    {
        $reached_points = 0;
        
        $selected_options = $answer->getSelectedIds();
        
        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions()->getOptions() as $answer_option) {
            if (in_array($answer_option->getOptionId(), $selected_options)) {
                $reached_points += $answer_option->getScoringDefinition()->getPointsSelected();
            } else {
                $reached_points += $answer_option->getScoringDefinition()->getPointsUnselected();
            }
        }
        return $reached_points;
    }
    
    protected function calculateMaxScore() : float
    {
        return $this->score($this->getBestAnswer());
    }
    
    public function getBestAnswer(): Answer
    {
        $answers = [];

        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions()->getOptions() as $answer_option) {
            /** @var MultipleChoiceScoringDefinition $score */
            $score = $answer_option->getScoringDefinition();
            if ($score->getPointsSelected() > $score->getPointsUnselected()) {
                $answers[] = $answer_option->getOptionId();
            }
        }

        rsort($answers);

        $length = $this->question->getPlayConfiguration()
            ->getEditorConfiguration()
            ->getMaxAnswers();
        $answers = array_slice($answers, 0, $length);

        return MultipleChoiceAnswer::create($answers);
    }

    protected function calculateMinScore() : float {
        $min = 0.0;
        
        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions()->getOptions() as $answer_option) {
            /** @var MultipleChoiceScoringDefinition $score */
            $score = $answer_option->getScoringDefinition();
            $min += min($score->getPointsSelected(), $score->getPointsUnselected());
        }
        
        return $this->calculateMaxHintDeduction() + $min;
    }
    
    public static function readConfig()
    {
        return MultipleChoiceScoringConfiguration::create();
    }
    
    public static function isComplete(Question $question): bool
    {    
        if (count($question->getAnswerOptions()->getOptions()) < 2) {
            return false;
        }

        foreach ($question->getAnswerOptions()->getOptions() as $option) {
            /** @var MultipleChoiceScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (is_null($option_config->getPointsSelected()) ||
                is_null($option_config->getPointsUnselected()))
            {
                return false;
            }
        }

        return true;
    }
}