<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Formula\FormulaAnswer;
use srag\asq\Questions\Formula\Editor\FormulaEditor;
use srag\asq\Questions\Formula\Editor\Data\FormulaEditorConfiguration;
use srag\asq\Questions\Formula\Form\FormulaFormFactory;
use srag\asq\Questions\Formula\Scoring\FormulaScoring;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringConfiguration;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringDefinition;
use srag\asq\Questions\Formula\Scoring\Data\FormulaScoringVariable;
use srag\asq\Questions\Generic\Data\EmptyDefinition;

/**
 * Class FormulaTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaTest extends QuestionTestCase
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
                    new FormulaEditorConfiguration(),
                    new FormulaScoringConfiguration(
                        '$r1 = ($v1 + $v2) / $v3',
                        'm, cm',
                        2,
                        10,
                        FormulaScoringConfiguration::TYPE_ALL,
                        [
                            new FormulaScoringVariable(2, 10, 'm', 1),
                            new FormulaScoringVariable(2, 8, 'm', 2),
                            new FormulaScoringVariable(2, 5, 'm', 1)
                        ])
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new FormulaScoringDefinition('($v1 + $v2) / $v3', 'cm', 2))
                ]
                ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new FormulaEditorConfiguration(),
                    new FormulaScoringConfiguration(
                        '$r1 = ($v1 + $v2) / $v3',
                        'm, cm',
                        2,
                        10,
                        FormulaScoringConfiguration::TYPE_DECIMAL,
                        [
                            new FormulaScoringVariable(2, 10, 'm', 1),
                            new FormulaScoringVariable(2, 8, 'm', 2),
                            new FormulaScoringVariable(2, 5, 'm', 1)
                        ])
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new FormulaScoringDefinition('($v1 + $v2) / $v3', 'cm', 2))

                ]
                ),
            'question 3' => $this->createQuestion(
                new QuestionData('Question 3', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new FormulaEditorConfiguration(),
                    new FormulaScoringConfiguration(
                        '$r1 = ($v1 + $v2) / $v3',
                        'm, cm',
                        2,
                        10,
                        FormulaScoringConfiguration::TYPE_COPRIME_FRACTION,
                        [
                            new FormulaScoringVariable(2, 10, 'm', 1),
                            new FormulaScoringVariable(2, 8, 'm', 2),
                            new FormulaScoringVariable(2, 5, 'm', 1)
                        ])
                    ),
                [
                    new AnswerOption('1',
                        new EmptyDefinition(),
                        new FormulaScoringDefinition('($v1 + $v2) / $v3', 'cm', 2))
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
            'answer 1' => new FormulaAnswer(['$v1' => '4', '$v2' => '6', '$v3' => '4', '$r1' => '2.5', '$r1fe_unit' => 'cm']),
            'answer 2' => new FormulaAnswer(['$v1' => '4', '$v2' => '6', '$v3' => '4', '$r1' => '5 / 2', '$r1fe_unit' => 'cm']),
            'answer 3' => new FormulaAnswer(['$v1' => '4', '$v2' => '6', '$v3' => '4', '$r1' => '10 / 4', '$r1fe_unit' => 'cm']),
            'answer 4' => new FormulaAnswer()
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
                'answer 2' => 2,
                'answer 3' => 2,
                'answer 4' => 0
            ],
            'question 2' => [
                'answer 1' => 2,
                'answer 2' => 0,
                'answer 3' => 0,
                'answer 4' => 0
            ],
            'question 3' => [
                'answer 1' => 0,
                'answer 2' => 2,
                'answer 3' => 0,
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
            'question 2' => 2,
            'question 3' => 2,
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return new QuestionType(
            'formula',
            FormulaFormFactory::class,
            FormulaEditor::class,
            FormulaScoring::class
        );
    }
}
