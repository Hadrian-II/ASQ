<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use AsqQuestionPageGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\QuestionComponent;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class UIService
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class UIService {
    /**
     * @param QuestionDto $question
     * @return QuestionComponent
     */
    public function getQuestionComponent(QuestionDto $question) : QuestionComponent {
        global $DIC;
        
        $DIC->language()->loadLanguageModule('asq');
        
        return new QuestionComponent($question);
    }
    
    /**
     * @param QuestionDto $question
     * @return QuestionFormGUI
     */
    public function getQuestionEditForm(QuestionDto $question) : QuestionFormGUI {
        $class = $question->getType()->getFormClass();
        return new $class($question);
    }
    
    /**
     * @param QuestionDto $question_dto
     * @return AsqQuestionPageGUI
     */
    public function getQuestionPage(QuestionDto $question_dto) : AsqQuestionPageGUI {
        $page_gui = new AsqQuestionPageGUI($question_dto->getContainerObjId(), $question_dto->getQuestionIntId());
        $page_gui->setRenderPageContainer(false);
        $page_gui->setEditPreview(true);
        $page_gui->setEnabledTabs(false);
        $page_gui->setPresentationTitle($question_dto->getData()->getTitle());
        
        $question_component = $this->getQuestionComponent($question_dto);
        $page_gui->setQuestionComponent($question_component);
        
        return $page_gui;
    }
}