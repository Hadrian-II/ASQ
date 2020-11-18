<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Essay\EssayAnswer;
use srag\asq\Questions\Essay\Editor\EssayEditor;
use srag\asq\Questions\Essay\Editor\Data\EssayEditorConfiguration;
use srag\asq\Questions\Essay\Form\EssayFormFactory;
use srag\asq\Questions\Essay\Scoring\EssayScoring;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringConfiguration;
use srag\asq\Questions\Essay\Scoring\Data\EssayScoringDefinition;
use srag\asq\Questions\Generic\Data\EmptyDefinition;

/**
 * Class EssayTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayTest extends QuestionTestCase
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
                    new EssayEditorConfiguration(),
                    new EssayScoringConfiguration(TextScoring::TM_CASE_INSENSITIVE, EssayScoring::SCORING_AUTOMATIC_ALL, 1)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('lorem', null)),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('ipsum', null)),
                    new AnswerOption('3',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('verylongword', null))
                ]
            ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new EssayEditorConfiguration(),
                    new EssayScoringConfiguration(TextScoring::TM_CASE_SENSITIVE, EssayScoring::SCORING_AUTOMATIC_ANY)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('lorem', 1)),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('ipsum', 1)),
                    new AnswerOption('3',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('verylongword', 2))
                ]
            ),
            'question 3' => $this->createQuestion(
                new QuestionData('Question 3', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new EssayEditorConfiguration(),
                    new EssayScoringConfiguration(TextScoring::TM_LEVENSHTEIN_2, EssayScoring::SCORING_AUTOMATIC_ONE, 3)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('lorem', null)),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('ipsum', null)),
                    new AnswerOption('3',
                        new EmptyDefinition(),
                        new EssayScoringDefinition('verylongword', null))
                ]
            ),
            'question 4' => $this->createQuestion(
                new QuestionData('Question 4', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new EssayEditorConfiguration(),
                    new EssayScoringConfiguration(null, EssayScoring::SCORING_MANUAL, 5)
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
            'answer 1' => new EssayAnswer(),
            'answer 2' => new EssayAnswer('lorem ipsum verylongword'),
            'answer 3' => new EssayAnswer('lorem IPSUM verylongword'),
            'answer 4' => new EssayAnswer('lorem Ipsum dolor'),
            'answer 5' => new EssayAnswer('vxxrylongword'),
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
                'answer 3' => 1,
                'answer 4' => 0,
                'answer 5' => 0
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 4,
                'answer 3' => 3,
                'answer 4' => 1,
                'answer 5' => 0
            ],
            'question 3' => [
                'answer 1' => 0,
                'answer 2' => 3,
                'answer 3' => 3,
                'answer 4' => 3,
                'answer 5' => 3
            ],
            'question 4' => [
                'answer 1' => new AsqException('Cant automatically score questions that have manual scoring'),
                'answer 2' => new AsqException('Cant automatically score questions that have manual scoring'),
                'answer 3' => new AsqException('Cant automatically score questions that have manual scoring'),
                'answer 4' => new AsqException('Cant automatically score questions that have manual scoring'),
                'answer 5' => new AsqException('Cant automatically score questions that have manual scoring')
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
            'question 2' => 4,
            'question 3' => 3,
            'question 4' => 5
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return new QuestionType(
            'essay',
            EssayFormFactory::class,
            EssayEditor::class,
            EssayScoring::class
            );
    }
}
