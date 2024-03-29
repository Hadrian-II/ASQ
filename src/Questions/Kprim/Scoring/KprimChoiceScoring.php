<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Scoring;

use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\Kprim\KprimChoiceAnswer;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringConfiguration;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringDefinition;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceScoring extends AbstractScoring
{
    public function score(?AbstractValueObject $answer) : float
    {
        if ($answer === null) {
            return 0;
        }

        $count = 0;
        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var KprimChoiceScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();
            $current_answer = $answer->getAnswerForId($option->getOptionId());
            if (!is_null($current_answer)) {
                if ($current_answer == true && $scoring_definition->isCorrectValue() ||
                    $current_answer == false && !$scoring_definition->isCorrectValue()) {
                    $count += 1;
                }
            }
        }

        /** @var KprimChoiceScoringConfiguration $scoring_conf */
        $scoring_conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        if ($count === count($this->question->getAnswerOptions())) {
            return $scoring_conf->getPoints();
        } elseif (!is_null($scoring_conf->getHalfPointsAt()) &&
                 $count >= $scoring_conf->getHalfPointsAt()) {
            return floor($scoring_conf->getPoints() / 2);
        } else {
            return 0;
        }
    }

    protected function calculateMaxScore() : float
    {
        return $this->question->getPlayConfiguration()->getScoringConfiguration()->getPoints();
    }

    public function getBestAnswer() : AbstractValueObject
    {
        $answers = [];

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var KprimChoiceScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();

            if ($scoring_definition->isCorrectValue()) {
                $answers[$option->getOptionId()] = true;
            } else {
                $answers[$option->getOptionId()] = false;
            }
        }

        return new KprimChoiceAnswer($answers);
    }

    public function isComplete() : bool
    {
        if (is_null($this->question->getPlayConfiguration()->getScoringConfiguration()->getPoints())) {
            return false;
        }

        if (count($this->question->getAnswerOptions()) < 1) {
            return false;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var KprimChoiceScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (is_null($option_config->isCorrectValue())) {
                return false;
            }
        }

        return true;
    }
}
