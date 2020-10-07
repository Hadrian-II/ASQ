<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\ErrorText\ErrorTextAnswer;
use srag\asq\Questions\ErrorText\Editor\ErrorTextEditor;
use srag\asq\Questions\ErrorText\Editor\Data\ErrorTextEditorConfiguration;
use srag\asq\Questions\ErrorText\Form\ErrorTextFormFactory;
use srag\asq\Questions\ErrorText\Scoring\ErrorTextScoring;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringConfiguration;
use srag\asq\Questions\ErrorText\Scoring\Data\ErrorTextScoringDefinition;
use srag\asq\Questions\Generic\Data\EmptyDefinition;

/**
 * Class ErrorTextTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextTest extends QuestionTestCase
{
    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getQuestions()
     */
    public function getQuestions() : array
    {
        return [
            'question 1' => $this->createQuestion(
                QuestionData::create('Question 1', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    ErrorTextEditorConfiguration::create('word1 word2 word3 word4 word5', null),
                    ErrorTextScoringConfiguration::create(1)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        ErrorTextScoringDefinition::create(0, 1, 'correct1', 1)),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        ErrorTextScoringDefinition::create(2, 2, 'correct2 correct2', 2)),
                    ])
                ),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    ErrorTextEditorConfiguration::create('word1 word2 word3 word4 word5 word6', 100),
                    ErrorTextScoringConfiguration::create(2)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        ErrorTextScoringDefinition::create(3, 3, 'correct3 correct3 correct3', 3))
                    ])
                )
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getAnswers()
     */
    public function getAnswers() : array
    {
        return [
            'answer 1' => ErrorTextAnswer::create(),
            'answer 2' => ErrorTextAnswer::create([0, 1]),
            'answer 3' => ErrorTextAnswer::create([0, 2, 3]),
            'answer 4' => ErrorTextAnswer::create([3, 4, 5]),
            'answer 5' => ErrorTextAnswer::create([0, 1, 2, 3, 4])
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getExpectedScores()
     */
    public function getExpectedScores() : array
    {
        return [
            'question 1' => [
                'answer 1' => 0,
                'answer 2' => 0,
                'answer 3' => 3,
                'answer 4' => -3,
                'answer 5' => 1
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => -4,
                'answer 3' => -6,
                'answer 4' => 3,
                'answer 5' => -10
            ]
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getMaxScores()
     */
    public function getMaxScores() : array
    {
        return [
            'question 1' => 3,
            'question 2' => 3
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'errortext',
            ErrorTextFormFactory::class,
            ErrorTextEditor::class,
            ErrorTextScoring::class
            );
    }
}
