<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use Exception;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class FileUploadScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadScoring extends AbstractScoring
{
    /**
     * @var FileUploadScoringConfiguration
     */
    protected $configuration;

    /**
     * @param QuestionDto $question
     */
    public function __construct($question) {
        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();
    }

    function score(Answer $answer) : float {
        $reached_points = 0;

        if ($this->configuration->isCompletedBySubmition()) {
            $reached_points = $this->getMaxScore();
        }
        else {
            // TODO go look for manual scoring or throw exception
        }

        return $reached_points;
    }

    protected function calculateMaxScore() : float
    {
        return $this->configuration->getPoints();
    }

    /**
     * Implementation of best answer for upload not possible
     */
    public function getBestAnswer(): Answer
    {
        throw new Exception("Best Answer for File Upload Impossible");
    }

    /**
     * @return string
     */
    public static function getScoringDefinitionClass(): string {
        return EmptyDefinition::class;
    }

    public function isComplete(): bool
    {
        // file upload can roll with all values
        return true;
    }
}