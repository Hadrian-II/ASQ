<?php
declare(strict_types=1);

namespace srag\asq\Domain\Definitions;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Application\Exception\AsqException;

/**
 * Interface IAsqQuestionEditor
 *
 * Defines an Asq Scoring element and is able to score given answers to a specific question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
interface IAsqQuestionScoring
{
    const ANSWER_CORRECT = 1;
    const ANSWER_INCORRECT = 2;
    const ANSWER_CORRECTNESS_NOT_DETERMINABLE = 3;

    /**
     * Checks if the given question contains all necessary data to perform automatic scoring
     *
     * @return bool
     */
    public function isComplete() : bool;

    /**
     * Gives the Score of an answer to a question
     *
     * Throws exception if automatic scoring not possible
     *
     * @param AbstractValueObject $answer
     * @return float
     * @throws AsqException
     */
    public function score(AbstractValueObject $answer) : float;

    /**
     * Gives the maximal possible score of a question
     *
     * Throws exception if automatic scoring not possible
     *
     * @return float
     * @throws AsqException
     */
    public function getMaxScore() : float;

    /**
     * Gives the minimal possible score of a question
     *
     * Throws exception if automatic scoring not possible
     *
     * @return float
     * @throws AsqException
     */
    public function getMinScore() : float;

    /**
     * Checks if an answer is considered correct
     * Usually correct implies maximal possible score
     *
     * returns ANSWER_CORRECT on correct and ANSWER_INCORRECT if not
     *
     * Throws exception if automatic scoring not possible
     *
     * @param AbstractValueObject $given_answer
     * @return int
     * @throws AsqException
     */
    public function getAnswerFeedbackType(AbstractValueObject $given_answer) : int;

    /**
     * Returns the maximal possible score
     *
     * Throws exception if automatic scoring not possible
     *
     * @return AbstractValueObject
     * @throws AsqException
     */
    public function getBestAnswer() : AbstractValueObject;
}
