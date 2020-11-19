<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Generic\Data\EmptyDefinition;
use srag\asq\Questions\TextSubset\TextSubsetAnswer;
use srag\asq\Questions\TextSubset\Editor\TextSubsetEditor;
use srag\asq\Questions\TextSubset\Editor\Data\TextSubsetEditorConfiguration;
use srag\asq\Questions\TextSubset\Form\TextSubsetFormFactory;
use srag\asq\Questions\TextSubset\Scoring\TextSubsetScoring;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringConfiguration;
use srag\asq\Questions\TextSubset\Scoring\Data\TextSubsetScoringDefinition;


/**
 * Class TextSubsetTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetTest extends QuestionTestCase
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
                    new TextSubsetEditorConfiguration(2),
                    new TextSubsetScoringConfiguration(TextScoring::TM_CASE_INSENSITIVE)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(1, 'aaaaaa')),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(2, 'bbbbbb')),
                    new AnswerOption('3',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(3, 'cccccc')),
                    ]
                ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new TextSubsetEditorConfiguration(3),
                    new TextSubsetScoringConfiguration(TextScoring::TM_CASE_SENSITIVE)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(1, 'aaaAaa')),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(2, 'Bbbbbb')),
                    new AnswerOption('3',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(3, 'cccccC')),
                ]
            ),
            'question 3' => $this->createQuestion(
                new QuestionData('Question 3', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new TextSubsetEditorConfiguration(3),
                    new TextSubsetScoringConfiguration(TextScoring::TM_LEVENSHTEIN_2)
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(1, 'bbaaaa')),
                    new AnswerOption('2',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(2, 'bbccbb')),
                    new AnswerOption('3',
                        new EmptyDefinition(),
                        new TextSubsetScoringDefinition(3, 'ccccdd')),
                ]
            ),
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getAnswers()
     */
    public function getAnswers() : array
    {
        return [
            'answer 1' => new TextSubsetAnswer(),
            'answer 2' => new TextSubsetAnswer([1 => 'bbbbbb', 2 => 'cccccc']),
            'answer 3' => new TextSubsetAnswer([1 => 'AAAAAA', 2 => 'CCCCCC']),
            'answer 4' => new TextSubsetAnswer([1 => 'aaaaaa', 2 => 'bbbbbb', 3 => 'cccccc']),
            'answer 5' => new TextSubsetAnswer([1 => 'aaaAaa', 2 => 'Bbbbbb', 3 => 'cccccC']),
            'answer 6' => new TextSubsetAnswer([1 => 'bbbaaa', 2 => 'bbbbbb', 3 => 'dccccc']),
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
                'answer 2' => 5,
                'answer 3' => 4,
                'answer 4' => new AsqException('Too many answers "3" given for maximum allowed of: "2"'),
                'answer 5' => new AsqException('Too many answers "3" given for maximum allowed of: "2"'),
                'answer 6' => new AsqException('Too many answers "3" given for maximum allowed of: "2"'),
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 0,
                'answer 3' => 0,
                'answer 4' => 0,
                'answer 5' => 6,
                'answer 6' => 0
            ],
            'question 3' => [
                'answer 1' => 0,
                'answer 2' => 5,
                'answer 3' => 0,
                'answer 4' => 6,
                'answer 5' => 3,
                'answer 6' => 3
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
            'question 1' => 5,
            'question 2' => 6,
            'question 3' => 6
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'textsubset',
            TextSubsetFormFactory::class,
            TextSubsetEditor::class,
            TextSubsetScoring::class
        );
    }
}
