<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Numeric\NumericAnswer;
use srag\asq\Questions\Numeric\Editor\Data\NumericEditorConfiguration;
use srag\asq\Questions\Numeric\Form\NumericFormFactory;
use srag\asq\Questions\Numeric\Scoring\Data\NumericScoringConfiguration;
use srag\asq\Questions\Numeric\Editor\NumericEditor;
use srag\asq\Questions\Numeric\Scoring\NumericScoring;

/**
 * Class NumericQuestionTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericTest extends QuestionTestCase
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
                    new NumericEditorConfiguration(),
                    new NumericScoringConfiguration(2, 2, 2)
                ),
                null
            ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new NumericEditorConfiguration(),
                    new NumericScoringConfiguration(3, 3, 4)
                ),
                null
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
            'answer 1' => new NumericAnswer(2),
            'answer 2' => new NumericAnswer(3),
            'answer 3' => new NumericAnswer(4),
            'answer 4' => new NumericAnswer(5)
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
                'answer 1' => 2,
                'answer 2' => 0,
                'answer 3' => 0,
                'answer 4' => 0
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 3,
                'answer 3' => 3,
                'answer 4' => 0
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
            'question 1' => 2,
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
            'numeric',
            NumericFormFactory::class,
            NumericEditor::class,
            NumericScoring::class
        );
    }
}
