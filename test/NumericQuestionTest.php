<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\QuestionTypeDefinition;
use srag\asq\Questions\Numeric\NumericEditorConfiguration;
use srag\asq\Questions\Numeric\NumericScoringConfiguration;
use srag\asq\Questions\Numeric\NumericAnswer;
use srag\asq\Questions\Numeric\Form\NumericFormFactory;
use srag\asq\Infrastructure\Persistence\QuestionType;

require_once 'QuestionTestCase.php';

/**
 * Class NumericQuestionTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericQuestionTest extends QuestionTestCase
{
    const TEST_CONTAINER = -1;
    const DONT_TEST = -1;

    public function getQuestions() : array
    {
        return [
            'question 1' => $this->createQuestion(
                QuestionData::create('Question 1', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    NumericEditorConfiguration::create(),
                    NumericScoringConfiguration::create(2, 2, 2)),
                null),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    NumericEditorConfiguration::create(),
                    NumericScoringConfiguration::create(2, 3, 4)),
                null)
        ];
    }

    public function getAnswers() : array
    {
        return [
            'answer 1' => NumericAnswer::create(2),
            'answer 2' => NumericAnswer::create(3),
            'answer 3' => NumericAnswer::create(4),
            'answer 4' => NumericAnswer::create(5)
        ];
    }

    public function getExpectedScore($question_id, $answer_id) : float
    {
        $data = [
            'question 1' => [
                'answer 1' => 2,
                'answer 2' => 0,
                'answer 3' => 0,
                'answer 4' => 0
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 2,
                'answer 3' => 2,
                'answer 4' => 0
            ]
        ];

        return $data[$question_id][$answer_id];
    }


    public function getTypeDefinition(): QuestionTypeDefinition
    {
        return QuestionTypeDefinition::create(QuestionType::createNew('numeric', NumericFormFactory::class));
    }
}
