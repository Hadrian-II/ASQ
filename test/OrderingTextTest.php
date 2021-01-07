<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Ordering\OrderingAnswer;
use srag\asq\Questions\Ordering\Editor\OrderingEditor;
use srag\asq\Questions\Ordering\Editor\Data\OrderingEditorConfiguration;
use srag\asq\Questions\Ordering\Form\OrderingTextFormFactory;
use srag\asq\Questions\Ordering\Scoring\OrderingScoring;
use srag\asq\Questions\Ordering\Scoring\Data\OrderingScoringConfiguration;


/**
 * Class OrderingTextTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTextTest extends QuestionTestCase
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
                    new OrderingEditorConfiguration(false, 'lorem ipsum dolor sit'),
                    new OrderingScoringConfiguration(1)
                    ),
                [
                    new AnswerOption('1',
                        new ImageAndTextDisplayDefinition('lorem'),
                        new EmptyDefinition()),
                    new AnswerOption('2',
                        new ImageAndTextDisplayDefinition('ipsum'),
                        new EmptyDefinition()),
                    new AnswerOption('3',
                        new ImageAndTextDisplayDefinition('dolor'),
                        new EmptyDefinition()),
                    new AnswerOption('4',
                        new ImageAndTextDisplayDefinition('sit'),
                        new EmptyDefinition())
                ]
            ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new OrderingEditorConfiguration(false, 'one two three four'),
                    new OrderingScoringConfiguration(2)
                    ),
                [
                    new AnswerOption('1',
                        new ImageAndTextDisplayDefinition('one'),
                        new EmptyDefinition()),
                    new AnswerOption('2',
                        new ImageAndTextDisplayDefinition('two'),
                        new EmptyDefinition()),
                    new AnswerOption('3',
                        new ImageAndTextDisplayDefinition('three'),
                        new EmptyDefinition()),
                    new AnswerOption('4',
                        new ImageAndTextDisplayDefinition('four'),
                        new EmptyDefinition())
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
            'answer 1' => new OrderingAnswer(),
            'answer 2' => new OrderingAnswer([1,2,3,4]),
            'answer 3' => new OrderingAnswer([1,3,2,4])
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
            'orderingtext',
            OrderingTextFormFactory::class,
            OrderingEditor::class,
            OrderingScoring::class
            );
    }
}
