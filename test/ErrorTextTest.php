<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
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
                new QuestionData('Question 1', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new ErrorTextEditorConfiguration('word1 word2 word3 word4 word5', null),
                    new ErrorTextScoringConfiguration(1)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new ErrorTextScoringDefinition(0, 1, 'correct1', 1)),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new ErrorTextScoringDefinition(2, 2, 'correct2 correct2', 2)),
                    ]
                ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new ErrorTextEditorConfiguration('word1 word2 word3 word4 word5 word6', 100),
                    new ErrorTextScoringConfiguration(2)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new ErrorTextScoringDefinition(3, 3, 'correct3 correct3 correct3', 3))
                ]
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
            'answer 1' => new ErrorTextAnswer(),
            'answer 2' => new ErrorTextAnswer([0, 1]),
            'answer 3' => new ErrorTextAnswer([0, 2, 3]),
            'answer 4' => new ErrorTextAnswer([3, 4, 5]),
            'answer 5' => new ErrorTextAnswer([0, 1, 2, 3, 4])
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
