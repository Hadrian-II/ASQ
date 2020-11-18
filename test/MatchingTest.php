<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\Matching\MatchingAnswer;
use srag\asq\Questions\Matching\Editor\MatchingEditor;
use srag\asq\Questions\Matching\Editor\Data\MatchingEditorConfiguration;
use srag\asq\Questions\Matching\Editor\Data\MatchingItem;
use srag\asq\Questions\Matching\Editor\Data\MatchingMapping;
use srag\asq\Questions\Matching\Form\MatchingFormFactory;
use srag\asq\Questions\Matching\Scoring\MatchingScoring;
use srag\asq\Questions\Matching\Scoring\Data\MatchingScoringConfiguration;
use srag\asq\Application\Exception\AsqException;

/**
 * Class MatchingTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingTest extends QuestionTestCase
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
                    new MatchingEditorConfiguration(
                        MatchingEditorConfiguration::SHUFFLE_NONE,
                        100,
                        MatchingEditorConfiguration::MATCHING_ONE_TO_ONE,
                        [
                            new MatchingItem('1', 'a', 'image.jpg'),
                            new MatchingItem('2', 'b', 'image.jpg'),
                            new MatchingItem('3', 'c', 'image.jpg'),
                            new MatchingItem('4', 'd', 'image.jpg')
                        ],
                        [
                            new MatchingItem('1', '1', 'image.jpg'),
                            new MatchingItem('2', '2', 'image.jpg'),
                            new MatchingItem('3', '3', 'image.jpg'),
                            new MatchingItem('4', '4', 'image.jpg')
                        ],
                        [
                            new MatchingMapping('1', '1', 2),
                            new MatchingMapping('2', '2', 2),
                            new MatchingMapping('3', '3', 2)
                        ]),
                    new MatchingScoringConfiguration(1)
                    ),
                null),
            'question 2' => $this->createQuestion(
                new QuestionData('Question 2', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new MatchingEditorConfiguration(
                        MatchingEditorConfiguration::SHUFFLE_DEFINITIONS,
                        100,
                        MatchingEditorConfiguration::MATCHING_MANY_TO_ONE,
                        [
                            new MatchingItem('1', 'a', 'image.jpg'),
                            new MatchingItem('2', 'b', 'image.jpg'),
                            new MatchingItem('3', 'c', 'image.jpg'),
                            new MatchingItem('4', 'd', 'image.jpg')
                        ],
                        [
                            new MatchingItem('1', '1', 'image.jpg'),
                            new MatchingItem('2', '2', 'image.jpg'),
                            new MatchingItem('3', '3', 'image.jpg'),
                            new MatchingItem('4', '4', 'image.jpg')
                        ],
                        [
                            new MatchingMapping('1', '1', 3),
                            new MatchingMapping('1', '2', 3),
                            new MatchingMapping('3', '3', 3),
                            new MatchingMapping('3', '4', 3)
                        ]),
                    new MatchingScoringConfiguration(2)
                    ),
                null),
            'question 3' => $this->createQuestion(
                new QuestionData('Question 3', '', '', '', 1),
                new QuestionPlayConfiguration(
                    new MatchingEditorConfiguration(
                        MatchingEditorConfiguration::SHUFFLE_BOTH,
                        125,
                        MatchingEditorConfiguration::MATCHING_MANY_TO_MANY,
                        [
                            new MatchingItem('1', 'a', 'image.jpg'),
                            new MatchingItem('2', 'b', 'image.jpg'),
                            new MatchingItem('3', 'c', 'image.jpg'),
                            new MatchingItem('4', 'd', 'image.jpg')
                        ],
                        [
                            new MatchingItem('1', '1', 'image.jpg'),
                            new MatchingItem('2', '2', 'image.jpg'),
                            new MatchingItem('3', '3', 'image.jpg'),
                            new MatchingItem('4', '4', 'image.jpg')
                        ],
                        [
                            new MatchingMapping('1', '1', 5),
                            new MatchingMapping('1', '2', 5),
                            new MatchingMapping('2', '1', 5),
                            new MatchingMapping('2', '2', 5),
                            new MatchingMapping('2', '3', 5),
                            new MatchingMapping('2', '4', 5)
                        ]),
                    new MatchingScoringConfiguration(3)
                    ),
                null)
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getAnswers()
     */
    public function getAnswers() : array
    {
        return [
            'answer 1' => new MatchingAnswer(),
            'answer 2' => new MatchingAnswer(['1-1', '2-2', '3-3']),
            'answer 3' => new MatchingAnswer(['1-1', '1-2', '3-3', '3-4']),
            'answer 4' => new MatchingAnswer(['1-1', '1-2', '2-1', '2-2', '2-3', '2-4']),
            'answer 5' => new MatchingAnswer(['1-1', '3-4', '2-3']),
            'answer 6' => new MatchingAnswer(['1-1', '1-1'])
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
                'answer 2' => 6,
                'answer 3' => 2,
                'answer 4' => 0,
                'answer 5' => 0,
                'answer 6' => new AsqException('One Matching was found multiple Times')
            ],
            'question 2' => [
                'answer 1' => 0,
                'answer 2' => 4,
                'answer 3' => 12,
                'answer 4' => -2,
                'answer 5' => 4,
                'answer 6' => new AsqException('One Matching was found multiple Times')
            ],
            'question 3' => [
                'answer 1' => 0,
                'answer 2' => 7,
                'answer 3' => 4,
                'answer 4' => 30,
                'answer 5' => 7,
                'answer 6' => new AsqException('One Matching was found multiple Times')
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getMaxScores()
     */
    public function getMaxScores() : array
    {
        return [
            'question 1' => 6,
            'question 2' => 12,
            'question 3' => 30
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return new QuestionType(
            'matching',
            MatchingFormFactory::class,
            MatchingEditor::class,
            MatchingScoring::class
        );
    }
}
