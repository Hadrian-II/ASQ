<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class FileUploadQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadQuestionGUI extends QuestionFormGUI
{
    protected function createDefaultPlayConfiguration() : QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            FileUploadEditorConfiguration::create(),
            FileUploadScoringConfiguration::create()
        );
    }
    
    protected function readPlayConfiguration() : QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            FileUploadEditor::readConfig(),
            FileUploadScoring::readConfig()
        );
    }
    
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play) : void
    {
        foreach (FileUploadEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (FileUploadScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
}
