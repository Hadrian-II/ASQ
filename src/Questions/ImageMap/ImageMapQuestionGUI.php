<?php
declare(strict_types=1);

namespace srag\asq\Questions\ImageMap;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Questions\MultipleChoice\MultipleChoiceScoring;
use srag\asq\Questions\MultipleChoice\MultipleChoiceScoringConfiguration;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
use srag\asq\UserInterface\Web\Fields\AsqImageUpload;

/**
 * Class ImageMapQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapQuestionGUI extends QuestionFormGUI {
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            ImageMapEditorConfiguration::create(),
            MultipleChoiceScoringConfiguration::create());
    }

    protected function canDisplayAnswerOptions() {
        return !empty($this->initial_question->getPlayConfiguration()->getEditorConfiguration()->getImage());
    }

    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            ImageMapEditor::readConfig(),
            MultipleChoiceScoring::readConfig());
    }

    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        foreach (ImageMapEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }

        foreach (MultipleChoiceScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }

    protected function postInit() {
        global $DIC;

        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/ImageMap/ImageMapAuthoring.js');
    }

    /**
     * @param QuestionDto $question
     * @return QuestionDto
     */
    protected function processPostQuestion(QuestionDto $question) : QuestionDto
    {
        /** @var AsqImageUpload $image_ctrl */
        $image_ctrl = $this->getItemByPostVar(ImageMapEditor::VAR_IMAGE);
        $image_ctrl->setImagePath($question->getPlayConfiguration()->getEditorConfiguration()->getImage());

        return $question;
    }
}