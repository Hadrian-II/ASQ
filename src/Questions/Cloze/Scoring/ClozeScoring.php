<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Scoring;

use ILIAS\UI\NotImplementedException;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;

/**
 * Class ClozeScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeScoring extends AbstractScoring
{
    /**
     * @var ClozeEditorConfiguration
     */
    protected $configuration;

    /**
     * @var TextScoring
     */
    private $text_scoring;

    /**
     * @param QuestionDto $question
     */
    public function __construct($question)
    {
        global $DIC;

        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->text_scoring = new TextScoring($DIC->language());
    }

    /**
     * @var float
     */
    private $reached_points;

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Definitions\IAsqQuestionScoring::score()
     */
    public function score(AbstractValueObject $answer) : float
    {
        $given_answer = $answer->getAnswers();

        $this->reached_points = 0.0;

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $gap_configuration = $this->configuration->getGaps()[$i - 1];

            if (! array_key_exists($i, $given_answer)) {
                continue;
            }

            if (get_class($gap_configuration) === SelectGapConfiguration::class) {
                $this->scoreSelectGap($given_answer[$i], $gap_configuration);
            } elseif (get_class($gap_configuration) === TextGapConfiguration::class) {
                $this->scoreTextGap($given_answer[$i], $gap_configuration);
            } elseif (get_class($gap_configuration) === NumericGapConfiguration::class) {
                $this->scoreNumericGap(floatval($given_answer[$i]), $gap_configuration);
            }
        }

        return $this->reached_points;
    }

    /**
     * @param string $answer
     * @param SelectGapConfiguration $gap_configuration
     */
    private function scoreSelectGap(string $answer, SelectGapConfiguration $gap_configuration) : void
    {
        /** @var $gap ClozeGapItem */
        foreach ($gap_configuration->getItems() as $gap_item) {
            if ($answer === $gap_item->getText()) {
                $this->reached_points += $gap_item->getPoints();
            }
        }
    }

    /**
     * @param string $answer
     * @param TextGapConfiguration $gap_configuration
     */
    private function scoreTextGap(string $answer, TextGapConfiguration $gap_configuration) : void
    {
        /** @var $gap ClozeGapItem */
        foreach ($gap_configuration->getItems() as $gap_item) {
            if ($this->text_scoring->isMatch($answer, $gap_item->getText(), $gap_configuration->getMatchingMethod())) {
                $this->reached_points += $gap_item->getPoints();
            }
        }
    }

    /**
     * @param float $answer
     * @param NumericGapConfiguration $gap_configuration
     */
    private function scoreNumericGap(float $answer, NumericGapConfiguration $gap_configuration) : void
    {
        if ($gap_configuration->getUpper() >= $answer &&
            $gap_configuration->getLower() <= $answer) {
            $this->reached_points += $gap_configuration->getPoints();
        }
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        $max_score = 0.0;

        foreach ($this->configuration->getGaps() as $gap_configuration) {
            $max_score += $gap_configuration->getMaxPoints();
        }

        return $max_score;
    }

    public function getBestAnswer() : AbstractValueObject
    {
        //TODO implement me
        throw new NotImplementedException("Needs to implement ClozeScoring->getBestAnswer()");
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        return true;
    }
}
