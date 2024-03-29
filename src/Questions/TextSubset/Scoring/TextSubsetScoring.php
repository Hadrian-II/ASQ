<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Scoring;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\TextSubset\TextSubsetAnswer;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringConfiguration;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringDefinition;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;

/**
 * Class TextSubsetScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetScoring extends AbstractScoring
{
    protected AbstractValueObject $answer;

    public function score(?AbstractValueObject $answer) : float
    {
        if ($answer === null) {
            return 0;
        }

        $this->answer = $answer;

        $max_allowed = $this->question->getPlayConfiguration()->getEditorConfiguration()->getNumberOfRequestedAnswers();
        $given_answers = count($this->answer->getAnswers());

        if ($given_answers > $max_allowed) {
            throw new AsqException(
                sprintf(
                    'Too many answers "%s" given for maximum allowed of: "%s"',
                    $given_answers,
                    $max_allowed));
        }

        /** @var TextSubsetScoringConfiguration $scoring_conf */
        $scoring_conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        switch ($scoring_conf->getTextMatching()) {
            case TextScoring::TM_CASE_INSENSITIVE:
                return $this->caseInsensitiveScoring();
            case TextScoring::TM_CASE_SENSITIVE:
                return $this->caseSensitiveScoring();
            case TextScoring::TM_LEVENSHTEIN_1:
                return $this->levenshteinScoring(1);
            case TextScoring::TM_LEVENSHTEIN_2:
                return $this->levenshteinScoring(2);
            case TextScoring::TM_LEVENSHTEIN_3:
                return $this->levenshteinScoring(3);
            case TextScoring::TM_LEVENSHTEIN_4:
                return $this->levenshteinScoring(4);
            case TextScoring::TM_LEVENSHTEIN_5:
                return $this->levenshteinScoring(5);
        }

        throw new AsqException("Unknown Test Subset Scoring Method found");
    }

    public function getBestAnswer() : AbstractValueObject
    {
        $answers = [];

        $options = $this->question->getAnswerOptions();

        usort($options, function(AnswerOption $a, AnswerOption $b) {
            $apoints = $a->getScoringDefinition()->getPoints();
            $bpoints = $b->getScoringDefinition()->getPoints();

            if ($apoints === $bpoints) {
                return 0;
            }

            return  ($apoints > $bpoints) ? -1 : 1;
        });

        $ix = 0;
        foreach ($options as $option) {
            $answers[] = $option->getScoringDefinition()->getText();
            $ix += 1;

            if ($ix >= $this->question->getPlayConfiguration()->getEditorConfiguration()->getNumberOfRequestedAnswers()) {
                break;
            }
        }

        return new TextSubsetAnswer($answers);
    }

    protected function calculateMaxScore() : float
    {
        $amount = $this->question->getPlayConfiguration()->getEditorConfiguration()->getNumberOfRequestedAnswers();

        if (empty($amount)) {
            return 0;
        }

        $points = array_map(function ($option) {
            return $option->getScoringDefinition()->getPoints();
        }, $this->question->getAnswerOptions());

        rsort($points);

        return array_sum(array_slice($points, 0, $amount));
    }

    private function caseInsensitiveScoring() : float
    {
        $reached_points = 0;

        foreach ($this->getAnswers() as $result) {
            foreach ($this->question->getAnswerOptions() as $correct) {
                if (strtoupper($correct->getScoringDefinition()->getText()) === strtoupper($result)) {
                    $reached_points += $correct->getScoringDefinition()->getPoints();
                    break;
                }
            }
        }

        return $reached_points;
    }

    private function caseSensitiveScoring() : float
    {
        $reached_points = 0;

        foreach ($this->getAnswers() as $result) {
            foreach ($this->question->getAnswerOptions() as $correct) {
                if ($correct->getScoringDefinition()->getText() === $result) {
                    $reached_points += $correct->getScoringDefinition()->getPoints();
                    break;
                }
            }
        }

        return $reached_points;
    }

    private function levenshteinScoring(int $distance) : float
    {
        $reached_points = 0;

        foreach ($this->getAnswers() as $result) {
            foreach ($this->question->getAnswerOptions() as $correct) {
                if (levenshtein($correct->getScoringDefinition()->getText(), $result) <= $distance) {
                    $reached_points += $correct->getScoringDefinition()->getPoints();
                    break;
                }
            }
        }

        return $reached_points;
    }

    private function getAnswers() : array
    {
        return array_unique($this->answer->getAnswers());
    }

    function isComplete() : bool
    {
        /** @var TextSubsetScoringConfiguration $config */
        $config = $this->question->getPlayConfiguration()->getScoringConfiguration();

        if (empty($config->getTextMatching())) {
            return false;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var TextSubsetScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (empty($option_config->getText()) ||
                empty($option_config->getPoints())) {
                return false;
            }
        }

        return true;
    }
}
