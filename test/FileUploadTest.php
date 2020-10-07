<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\Configuration\QuestionPlayConfiguration;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\Questions\FileUpload\FileUploadAnswer;
use srag\asq\Questions\FileUpload\Editor\FileUploadEditor;
use srag\asq\Questions\FileUpload\Editor\Data\FileUploadEditorConfiguration;
use srag\asq\Questions\FileUpload\Form\FileUploadFormFactory;
use srag\asq\Questions\FileUpload\Scoring\FileUploadScoring;
use srag\asq\Questions\FileUpload\Scoring\Data\FileUploadScoringConfiguration;

/**
 * Class FileUploadTest
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadTest extends QuestionTestCase
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
                    FileUploadEditorConfiguration::create(),
                    FileUploadScoringConfiguration::create(2, true)
                    ),
                null
            ),
            'question 2' => $this->createQuestion(
                QuestionData::create('Question 2', '', '', '', 1),
                QuestionPlayConfiguration::create(
                    FileUploadEditorConfiguration::create(1000000, "png,jpg,funnyfile"),
                    FileUploadScoringConfiguration::create(1, false)
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
            'answer 1' => FileUploadAnswer::create(),
            'answer 2' => FileUploadAnswer::create([ 'file1' => 'blah.png', 'file2' => 'blubber.png'])
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
                'answer 2' => 2,
            ],
            'question 2' => [
                'answer 1' => new AsqException('Cant automatically score Fileupload'),
                'answer 2' => new AsqException('Cant automatically score Fileupload')
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
            'question 2' => 1
        ];
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\AssessmentQuestion\Test\QuestionTestCase::getTypeDefinition()
     */
    public function getTypeDefinition() : QuestionType
    {
        return QuestionType::createNew(
            'fileupload',
            FileUploadFormFactory::class,
            FileUploadEditor::class,
            FileUploadScoring::class
            );
    }
}
