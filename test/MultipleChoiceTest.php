<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Choice\MultipleChoiceAnswer;
use srag\asq\Questions\Choice\Editor\MultipleChoice\MultipleChoiceEditor;
use srag\asq\Questions\Choice\Editor\MultipleChoice\Data\MultipleChoiceEditorConfiguration;
use srag\asq\Questions\Choice\Form\Editor\MultipleChoice\MultipleChoiceFormFactory;
use srag\asq\Questions\Choice\Scoring\MultipleChoiceScoring;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringConfiguration;
use srag\asq\Questions\Choice\Scoring\Data\MultipleChoiceScoringDefinition;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;

/**
 * Class MultipleChoiceTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceTest extends QuestionTestCase
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
                    new MultipleChoiceEditorConfiguration(false, 1),
                    new MultipleChoiceScoringConfiguration()
                    ),
                    [
                        new AnswerOption('1',
                            new ImageAndTextDisplayDefinition('1', 'blah.jpg'),
                            new MultipleChoiceScoringDefinition(1, 0)),
                        new AnswerOption('2',
                            new ImageAndTextDisplayDefinition('2'),
                            new MultipleChoiceScoringDefinition(2, 0)),
                        new AnswerOption('3',
                            new ImageAndTextDisplayDefinition('3', 'blah.jpg'),
                            new MultipleChoiceScoringDefinition(3, 0)),
                        new AnswerOption('4',
                            new ImageAndTextDisplayDefinition('4'),
                            new MultipleChoiceScoringDefinition(4, 0))
                    ]
                ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new MultipleChoiceEditorConfiguration(true, 2, 100),
                    new MultipleChoiceScoringConfiguration()
                    ),
                [
                    new AnswerOption('1',
                        new ImageAndTextDisplayDefinition('1', 'blah.jpg'),
                        new MultipleChoiceScoringDefinition(1, 0)),
                    new AnswerOption('2',
                        new ImageAndTextDisplayDefinition('2'),
                        new MultipleChoiceScoringDefinition(0, 0)),
                    new AnswerOption('3',
                        new ImageAndTextDisplayDefinition('3', 'blah.jpg'),
                        new MultipleChoiceScoringDefinition(1, 0)),
                    new AnswerOption('4',
                        new ImageAndTextDisplayDefinition('4'),
                        new MultipleChoiceScoringDefinition(0, 1))
                ]
                ),
            'question 3' => $this->createQuestion(
                new QuestionData('Question 3', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new MultipleChoiceEditorConfiguration(false, 3, 100),
                    new MultipleChoiceScoringConfiguration()
                    ),
                [
                    new AnswerOption('1',
                        new ImageAndTextDisplayDefinition('1', 'blah.jpg'),
                        new MultipleChoiceScoringDefinition(2, -2)),
                    new AnswerOption('2',
                        new ImageAndTextDisplayDefinition('2'),
                        new MultipleChoiceScoringDefinition(1, 0)),
                    new AnswerOption('3',
                        new ImageAndTextDisplayDefinition('3', 'blah.jpg'),
                        new MultipleChoiceScoringDefinition(1, 0)),
                    new AnswerOption('4',
                        new ImageAndTextDisplayDefinition('4'),
                        new MultipleChoiceScoringDefinition(-1, 1))
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
            'answer 1' => new MultipleChoiceAnswer([]),
            'answer 2' => new MultipleChoiceAnswer(['1']),
            'answer 3' => new MultipleChoiceAnswer(['4']),
            'answer 4' => new MultipleChoiceAnswer(['1', '2']),
            'answer 5' => new MultipleChoiceAnswer(['1', '3']),
            'answer 6' => new MultipleChoiceAnswer(['1', '2', '3'])
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
                'answer 3' => 4,
                'answer 4' => new AsqException('Too many answers "2" given for maximum allowed of: "1"'),
                'answer 5' => new AsqException('Too many answers "2" given for maximum allowed of: "1"'),
                'answer 6' => new AsqException('Too many answers "3" given for maximum allowed of: "1"'),
            ],
            'question 2' => [
                'answer 1' => 1,
                'answer 2' => 2,
                'answer 3' => 0,
                'answer 4' => 2,
                'answer 5' => 3,
                'answer 6' => new AsqException('Too many answers "3" given for maximum allowed of: "2"')
            ],
            'question 3' => [
                'answer 1' => -1,
                'answer 2' => 3,
                'answer 3' => -3,
                'answer 4' => 4,
                'answer 5' => 4,
                'answer 6' => 5
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
            'question 1' => 4,
            'question 2' => 3,
            'question 3' => 5
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'multiple_choice',
            MultipleChoiceFormFactory::class,
            MultipleChoiceEditor::class,
            MultipleChoiceScoring::class
            );
    }
}
