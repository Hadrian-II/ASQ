<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\AsqGateway;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Feedback\Form\QuestionFeedbackFormGUI;

/**
 * Class AsqQuestionFeedbackEditorGUI
 *
 * GUI for editing question Feedback
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_Calls AsqQuestionFeedbackEditorGUI: ilAsqGenericFeedbackPageGUI
 * @ilCtrl_Calls AsqQuestionFeedbackEditorGUI: ilAsqAnswerOptionFeedbackPageGUI
 */
class AsqQuestionFeedbackEditorGUI
{

    const CMD_SHOW_FEEDBACK_FORM = 'showFeedbackForm';
    const CMD_SAVE_FEEDBACK = 'saveFeedback';

    /**
     * @var QuestionDto
     */
    protected $question_dto;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @var ilCtrl
     */
    private $ctrl;

    /**
     * @param QuestionDto $question_dto
     * @param ilLanguage $language
     * @param UIServices $ui
     * @param ilCtrl $ctrl
     */
    public function __construct(QuestionDto $question_dto, ilLanguage $language, UIServices $ui, ilCtrl $ctrl)
    {
        $this->question_dto = $question_dto;
        $this->language = $language;
        $this->ui = $ui;
        $this->ctrl = $ctrl;
    }


    /**
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_SHOW_FEEDBACK_FORM);
        $this->{$cmd}();
    }

    protected function saveFeedback() : void
    {
        $form = $this->createForm();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            $form->checkInput())
        {
            $new_feedback = $form->getFeedbackFromPost();
            $this->question_dto->setFeedback($new_feedback);
            AsqGateway::get()->question()->saveQuestion($this->question_dto);
            ilutil::sendSuccess("Question Saved", true);
        }

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }

    protected function showFeedbackForm() : void
    {
        $form = $this->createForm();

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * @return QuestionFeedbackFormGUI
     */
    private function createForm() : QuestionFeedbackFormGUI
    {
        $form = new QuestionFeedbackFormGUI($this->question_dto, $this->language);
        $form->setFormAction($this->ctrl->getFormAction($this, self::CMD_SHOW_FEEDBACK_FORM));
        $form->addCommandButton(self::CMD_SAVE_FEEDBACK, $this->language->txt('save'));
        $form->addCommandButton(self::CMD_SHOW_FEEDBACK_FORM, $this->language->txt('cancel'));
        return $form;
    }

}
