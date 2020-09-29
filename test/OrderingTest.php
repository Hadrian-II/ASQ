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
use srag\asq\Questions\Ordering\Form\OrderingFormFactory;
use srag\asq\Questions\Ordering\Editor\OrderingEditor;
use srag\asq\Questions\Ordering\Scoring\OrderingScoring;
use srag\asq\Questions\Ordering\Editor\Data\OrderingEditorConfiguration;
use srag\asq\Questions\Ordering\Scoring\Data\OrderingScoringConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Ordering\OrderingAnswer;

require_once 'QuestionTestCase.php';

/**
 * Class OrderingTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTest extends QuestionTestCase
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
                    OrderingEditorConfiguration::create(true),
                    OrderingScoringConfiguration::create(1)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        ImageAndTextDisplayDefinition::create('lorem', 'image.png'),
                        EmptyDefinition::create()),
                    AnswerOption::create('2',
                        ImageAndTextDisplayDefinition::create('ipsum', 'image.png'),
                        EmptyDefinition::create()),
                    AnswerOption::create('3',
                        ImageAndTextDisplayDefinition::create('dolor', 'image.png'),
                        EmptyDefinition::create()),
                    AnswerOption::create('4',
                        ImageAndTextDisplayDefinition::create('sit', 'image.png'),
                        EmptyDefinition::create()),
                ])
            ),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    OrderingEditorConfiguration::create(false),
                    OrderingScoringConfiguration::create(2)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        ImageAndTextDisplayDefinition::create('one'),
                        EmptyDefinition::create()),
                    AnswerOption::create('2',
                        ImageAndTextDisplayDefinition::create('two'),
                        EmptyDefinition::create()),
                    AnswerOption::create('3',
                        ImageAndTextDisplayDefinition::create('three'),
                        EmptyDefinition::create()),
                    AnswerOption::create('4',
                        ImageAndTextDisplayDefinition::create('four'),
                        EmptyDefinition::create()),
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
            'answer 1' => OrderingAnswer::create(),
            'answer 2' => OrderingAnswer::create([1,2,3,4]),
            'answer 3' => OrderingAnswer::create([1,3,2,4])
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
                'answer 2' => 1,
                'answer 3' => 0
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 2,
                'answer 3' => 0
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
            'question 1' => 1,
            'question 2' => 2
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'ordering',
            OrderingFormFactory::class,
            OrderingEditor::class,
            OrderingScoring::class
            );
    }
}
