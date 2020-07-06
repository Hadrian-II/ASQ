<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

require_once 'QuestionTestCase.php';

/**
 * Class ClozeQuestionTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeQuestionTest extends QuestionTestCase
{
    const TEST_CONTAINER = -1;
    const DONT_TEST = -1;

    public function getQuestions() : array
    {
        return [];
    }

    public function getAnswers() : array
    {
        return [];
    }

    public function getExpectedScore(string $question_id, string $answer_id) : float
    {
        return [];
    }
}
