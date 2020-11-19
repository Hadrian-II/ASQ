<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Kprim\KprimChoiceAnswer;
use srag\asq\Questions\Kprim\Editor\KprimChoiceEditor;
use srag\asq\Questions\Kprim\Editor\Data\KprimChoiceEditorConfiguration;
use srag\asq\Questions\Kprim\Form\KprimChoiceFormFactory;
use srag\asq\Questions\Kprim\Scoring\KprimChoiceScoring;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringConfiguration;
use srag\asq\Questions\Kprim\Scoring\Data\KprimChoiceScoringDefinition;

/**
 * Class KprimTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimTest extends QuestionTestCase
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
                    new KprimChoiceEditorConfiguration(true, 100, 'yay', 'nay'),
                    new KprimChoiceScoringConfiguration(4, 3)
                    ),
                [
                    new AnswerOption('1',
                        new ImageAndTextDisplayDefinition('bild1.jpg', 'lorem'),
                        new KprimChoiceScoringDefinition(true)),
                    new AnswerOption('2',
                        new ImageAndTextDisplayDefinition('bild2.jpg', 'ipsum'),
                        new KprimChoiceScoringDefinition(false)),
                    new AnswerOption('3',
                        new ImageAndTextDisplayDefinition('bild3.jpg', 'dolor'),
                        new KprimChoiceScoringDefinition(true)),
                    new AnswerOption('4',
                        new ImageAndTextDisplayDefinition('bild4.jpg', 'lorem'),
                        new KprimChoiceScoringDefinition(false))
                ]
            ),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new KprimChoiceEditorConfiguration(false, 80, 'true', 'false'),
                    new KprimChoiceScoringConfiguration(6, 2)
                    ),
                [
                    new AnswerOption('1',
                        new ImageAndTextDisplayDefinition('bild1.jpg', 'lorem'),
                        new KprimChoiceScoringDefinition(false)),
                    new AnswerOption('2',
                        new ImageAndTextDisplayDefinition('bild2.jpg', 'ipsum'),
                        new KprimChoiceScoringDefinition(true)),
                    new AnswerOption('3',
                        new ImageAndTextDisplayDefinition('bild3.jpg', 'dolor'),
                        new KprimChoiceScoringDefinition(false)),
                    new AnswerOption('4',
                        new ImageAndTextDisplayDefinition('bild4.jpg', 'lorem'),
                        new KprimChoiceScoringDefinition(true))
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
            'answer 1' => new KprimChoiceAnswer(['1' => true, '2' => false, '3' => true, '4' => false]),
            'answer 2' => new KprimChoiceAnswer(['1' => false, '2' => false, '3' => true, '4' => false]),
            'answer 3' => new KprimChoiceAnswer(['1' => false, '2' => true, '3' => true, '4' => false]),
            'answer 4' => new KprimChoiceAnswer(['1' => false, '2' => true, '3' => false, '4' => true])
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
                'answer 1' => 4,
                'answer 2' => 2,
                'answer 3' => 0,
                'answer 4' => 0
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 0,
                'answer 3' => 3,
                'answer 4' => 6
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
            'question 2' => 6
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'kprim',
            KprimChoiceFormFactory::class,
            KprimChoiceEditor::class,
            KprimChoiceScoring::class
        );
    }
}
