<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Cloze\ClozeAnswer;
use srag\asq\Questions\Cloze\Editor\ClozeEditor;
use srag\asq\Questions\Cloze\Editor\Data\ClozeEditorConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\ClozeGapItem;
use srag\asq\Questions\Cloze\Editor\Data\NumericGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\SelectGapConfiguration;
use srag\asq\Questions\Cloze\Editor\Data\TextGapConfiguration;
use srag\asq\Questions\Cloze\Form\ClozeFormFactory;
use srag\asq\Questions\Cloze\Scoring\ClozeScoring;
use srag\asq\Questions\Cloze\Scoring\Data\ClozeScoringConfiguration;

require_once 'NumericTest.php';

/**
 * Class ClozeTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeTest extends QuestionTestCase
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
                    ClozeEditorConfiguration::create('lorem {1} ipsum {2} dolor {3} sit', [
                        NumericGapConfiguration::Create(2, 3, 1, 2, 77),
                        SelectGapConfiguration::Create([
                            ClozeGapItem::create('asdf', 1),
                            ClozeGapItem::create('sdfg', 2),
                            ClozeGapItem::create('yxcv', 3)
                        ]),
                        TextGapConfiguration::Create([
                            ClozeGapItem::create('qqqq', 1),
                            ClozeGapItem::create('wwww', 2),
                            ClozeGapItem::create('eeee', 3)
                        ], 88, TextScoring::TM_CASE_INSENSITIVE)
                    ]),
                    ClozeScoringConfiguration::create()
                    ),
                null
                ),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    ClozeEditorConfiguration::create('lorem {1} ipsum {2} dolor {3} sit', [
                        NumericGapConfiguration::Create(3, 4, 2, 1, 66),
                        SelectGapConfiguration::Create([
                            ClozeGapItem::create('asdf', 2),
                            ClozeGapItem::create('sdfg', 1)
                        ]),
                        TextGapConfiguration::Create([
                            ClozeGapItem::create('qqqq', 2),
                            ClozeGapItem::create('wwww', 1)
                        ], 300, TextScoring::TM_CASE_SENSITIVE)
                    ]),
                    ClozeScoringConfiguration::create()
                    ),
                null
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
            'answer 1' => ClozeAnswer::create(['1' => '2', '2' => 'yxcv', '3' => 'eeee']),
            'answer 2' => ClozeAnswer::create(['1' => '3', '2' => 'asdf', '3' => 'qqqq']),
            'answer 3' => ClozeAnswer::create(['1' => '4', '2' => 'sdfg', '3' => 'Wwww']),
            'answer 4' => ClozeAnswer::create(['1' => '1', '2' => 'asdf', '3' => 'qqqq']),
            'answer 5' => ClozeAnswer::create(),
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
                'answer 1' => 8,
                'answer 2' => 4,
                'answer 3' => 4,
                'answer 4' => 4,
                'answer 5' => 0
            ],
            'question 2' => [
                'answer 1' => 1,
                'answer 2' => 5,
                'answer 3' => 2,
                'answer 4' => 4,
                'answer 5' => 0
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
            'question 1' => 8,
            'question 2' => 5
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'cloze',
            ClozeFormFactory::class,
            ClozeEditor::class,
            ClozeScoring::class
        );
    }
}
