<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
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

require_once 'QuestionTestCase.php';

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
                QuestionData::create('Question 1', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    EssayEditorConfiguration::create(),
                    EssayScoringConfiguration::create(TextScoring::TM_CASE_INSENSITIVE, EssayScoring::SCORING_AUTOMATIC_ALL, 1)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('lorem', null)),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('ipsum', null)),
                    AnswerOption::create('3',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('verylongword', null))
                ])
            ),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    EssayEditorConfiguration::create(),
                    EssayScoringConfiguration::create(TextScoring::TM_CASE_SENSITIVE, EssayScoring::SCORING_AUTOMATIC_ANY)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('lorem', 1)),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('ipsum', 1)),
                    AnswerOption::create('3',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('verylongword', 2))
                ])
            ),
            'question 3' => $this->createQuestion(
                QuestionData::create('Question 3', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    EssayEditorConfiguration::create(),
                    EssayScoringConfiguration::create(TextScoring::TM_LEVENSHTEIN_2, EssayScoring::SCORING_AUTOMATIC_ONE, 3)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('lorem', null)),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('ipsum', null)),
                    AnswerOption::create('3',
                        EmptyDefinition::create(),
                        EssayScoringDefinition::create('verylongword', null))
                ])
            ),
            'question 4' => $this->createQuestion(
                QuestionData::create('Question 4', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    EssayEditorConfiguration::create(),
                    EssayScoringConfiguration::create(null, EssayScoring::SCORING_MANUAL, 5)
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
            'answer 1' => EssayAnswer::create(),
            'answer 2' => EssayAnswer::create('lorem ipsum verylongword'),
            'answer 3' => EssayAnswer::create('lorem IPSUM verylongword'),
            'answer 4' => EssayAnswer::create('lorem Ipsum dolor'),
            'answer 5' => EssayAnswer::create('vxxrylongword'),
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
        return QuestionType::createNew(
            'essay',
            EssayFormFactory::class,
            EssayEditor::class,
            EssayScoring::class
            );
    }
}
