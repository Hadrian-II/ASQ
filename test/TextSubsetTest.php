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
                QuestionData::create('Question 1', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    TextSubsetEditorConfiguration::create(2),
                    TextSubsetScoringConfiguration::create(TextScoring::TM_CASE_INSENSITIVE)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(1, 'aaaaaa')),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(2, 'bbbbbb')),
                    AnswerOption::create('3',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(3, 'cccccc')),
                    ])
                ),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    TextSubsetEditorConfiguration::create(3),
                    TextSubsetScoringConfiguration::create(TextScoring::TM_CASE_SENSITIVE)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(1, 'aaaAaa')),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(2, 'Bbbbbb')),
                    AnswerOption::create('3',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(3, 'cccccC')),
                    ])
                ),
            'question 3' => $this->createQuestion(
                QuestionData::create('Question 3', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    TextSubsetEditorConfiguration::create(3),
                    TextSubsetScoringConfiguration::create(TextScoring::TM_LEVENSHTEIN_2)
                    ),
                AnswerOptions::create([
                    AnswerOption::create('1',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(1, 'bbaaaa')),
                    AnswerOption::create('2',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(2, 'bbccbb')),
                    AnswerOption::create('3',
                        EmptyDefinition::create(),
                        TextSubsetScoringDefinition::create(3, 'ccccdd')),
                ])
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
            'answer 1' => TextSubsetAnswer::create(),
            'answer 2' => TextSubsetAnswer::create([1 => 'bbbbbb', 2 => 'cccccc']),
            'answer 3' => TextSubsetAnswer::create([1 => 'AAAAAA', 2 => 'CCCCCC']),
            'answer 4' => TextSubsetAnswer::create([1 => 'aaaaaa', 2 => 'bbbbbb', 3 => 'cccccc']),
            'answer 5' => TextSubsetAnswer::create([1 => 'aaaAaa', 2 => 'Bbbbbb', 3 => 'cccccC']),
            'answer 6' => TextSubsetAnswer::create([1 => 'bbbaaa', 2 => 'bbbbbb', 3 => 'dccccc']),
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
