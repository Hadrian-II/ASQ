<?php
declare(strict_types = 1);
namespace srag\asq\Questions\ErrorText;

use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class ErrorTextScoring
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextScoring extends AbstractScoring
{
    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    function score(Answer $answer) : float
    {
        $reached_points = 0.0;

        $selected_words = $answer->getSelectedWordIndexes();
        $correct_words = [];

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();

            if (in_array($scoring_definition->getWrongWordIndex(), $selected_words)) {
                // multiple words '(( ))'
                if ($scoring_definition->getWrongWordLength() > 1) {
                    $correct = true;

                    for ($i = 0; $i < $scoring_definition->getWrongWordLength(); $i ++) {

                        $current = $scoring_definition->getWrongWordIndex() + $i;

                        if (! in_array($current, $selected_words)) {
                            $correct = false;
                            break;
                        }
                    }

                    if ($correct) {
                        for ($i = 0; $i < $scoring_definition->getWrongWordLength(); $i ++) {
                            $correct_words[] = $scoring_definition->getWrongWordIndex() + $i;
                        }
                        $reached_points += $scoring_definition->getPoints();
                    }
                } // single word '#'
                else {
                    $correct_words[] = $scoring_definition->getWrongWordIndex();
                    $reached_points += $scoring_definition->getPoints();
                }
            }
        }

        // deduct wrong selections
        $reached_points -= count(array_diff($selected_words, $correct_words));

        return $reached_points;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        $max_score = 0.0;

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();
            $max_score += $scoring_definition->getPoints();
        }

        return $max_score;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::getBestAnswer()
     */
    public function getBestAnswer() : Answer
    {
        $selected_word_indexes = [];

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();

            for ($i = 0; $i < $scoring_definition->getWrongWordLength(); $i ++) {

                $selected_word_indexes[] = $scoring_definition->getWrongWordIndex() + $i;
            }
        }

        return ErrorTextAnswer::create($selected_word_indexes);
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        /** @var ErrorTextScoringConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getScoringConfiguration();

        if (empty($config->getPointsWrong())) {
            return false;
        }

        if (count($this->question->getAnswerOptions()->getOptions()) < 1) {
            return false;
        }

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (empty($option_config->getPoints()) ||
                empty($option_config->getWrongWordIndex() ||
                empty($option_config->getWrongWordLength())))
            {
                return false;
            }
        }

        return true;
    }
}