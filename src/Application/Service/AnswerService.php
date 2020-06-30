<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;

/**
 * Class AnswerService
 *
 * Service for Question Answer processing
 * Contains methods for scoring and also an option to store Answers to DB
 * It is strongly recommended to store answers with the Test
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AnswerService extends ASQService
{
    /**
     * Gets the score of an answer to a question
     *
     * @param QuestionDto $question
     * @param Answer $answer
     * @return float
     */
    public function getScore(QuestionDto $question, Answer $answer) : float
    {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->score($answer);
    }

    /**
     * Gets the maximum score reachable for a question
     *
     * @param QuestionDto $question
     * @return float
     */
    public function getMaxScore(QuestionDto $question) : float
    {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->getMaxScore();
    }

    /**
     * Gets the minimum score reachable for a question
     *
     * @param QuestionDto $question
     * @return float
     */
    public function getMinScore(QuestionDto $question) : float
    {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->getMinScore();
    }

    /**
     * Gets the best possible answer to a question
     *
     * @param QuestionDto $question
     * @return Answer
     */
    public function getBestAnswer(QuestionDto $question) : Answer
    {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->getBestAnswer();
    }

    /**
     * Stores an answer to the database
     *
     * @param Answer $answer
     * @param string $uuid
     * @return string
     */
    public function storeAnswer(Answer $answer, ?string $uuid = null) : string
    {
        $stored = SimpleStoredAnswer::createNew($answer, $uuid);
        $stored->create();
        return $stored->getUuid();
    }

    /**
     * Read answer from the database
     *
     * @param string $uuid
     * @param int $version
     * @return Answer
     */
    public function getAnswer(string $uuid, ?int $version = null) : Answer
    {
        if (is_null($version)) {
            $stored = SimpleStoredAnswer::where(['uuid' => $uuid])->orderBy('version', 'DESC')->first();
        } else {
            $stored = SimpleStoredAnswer::where(['uuid' => $uuid, 'version' => $version])->first();
        }

        if (is_null($stored)) {
            throw new AsqException(sprintf('The requested Answer does not exist UUID = "%s" Version = "%s"', $uuid, $version));
        }

        return $stored->getAnswer();
    }

    /**
     * Gets all versions of a stored answer from database
     *
     * @param string $uuid
     * @return Answer[]
     */
    public function getAnswerHistory(string $uuid) : array
    {
        $history = SimpleStoredAnswer::where(['uuid' => $uuid])->get();

        $answers = [];

        foreach ($history as $stored_answer) {
            $answers[$stored_answer->getVersion()] = $stored_answer->getAnswer();
        }

        return $answers;
    }
}
