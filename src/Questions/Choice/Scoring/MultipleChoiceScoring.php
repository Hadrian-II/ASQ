<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Scoring;

use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Choice\MultipleChoiceAnswer;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Application\Exception\AsqException;

/**
 * Class MultipleChoiceScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceScoring extends AbstractScoring
{
    public function score(?AbstractValueObject $answer) : float
    {
        if ($answer === null) {
            return 0;
        }

        $reached_points = 0;

        $selected_options = $answer->getSelectedIds();
        $max_allowed = $this->question->getPlayConfiguration()->getEditorConfiguration()->getMaxAnswers();

        if (count($selected_options) > $max_allowed) {
            throw new AsqException(
                sprintf(
                    'Too many answers "%s" given for maximum allowed of: "%s"',
                    count($selected_options),
                    $max_allowed));
        }

        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions() as $answer_option) {
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

    public function getBestAnswer() : AbstractValueObject
    {
        $answers = [];

        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions() as $answer_option) {
            /** @var MultipleChoiceScoringDefinition $score */
            $score = $answer_option->getScoringDefinition();
            if ($score->getPointsSelected() > $score->getPointsUnselected()) {
                $answers[$answer_option->getOptionId()] = $score->getPointsSelected();
            }
        }

        asort($answers);
        $answers = array_reverse($answers, true);

        $length = $this->question->getPlayConfiguration()
            ->getEditorConfiguration()
            ->getMaxAnswers();
        $answers = array_slice($answers, 0, $length, true);

        return new MultipleChoiceAnswer(array_keys($answers));
    }

    protected function calculateMinScore() : float
    {
        $min = 0.0;

        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions() as $answer_option) {
            /** @var MultipleChoiceScoringDefinition $score */
            $score = $answer_option->getScoringDefinition();
            $min += min($score->getPointsSelected(), $score->getPointsUnselected());
        }

        return $this->calculateMaxHintDeduction() + $min;
    }

    public function isComplete() : bool
    {
        if (count($this->question->getAnswerOptions()) < 2) {
            return false;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var MultipleChoiceScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (is_null($option_config->getPointsSelected()) ||
                ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMaxAnswers() > 1 && is_null($option_config->getPointsUnselected()))) {
                return false;
            }
        }

        return true;
    }
}
