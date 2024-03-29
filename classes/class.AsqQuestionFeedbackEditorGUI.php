<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Feedback\Form\QuestionFeedbackFormGUI;
use srag\asq\Application\Service\ASQServices;

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

    protected QuestionDto $question_dto;

    private UIServices $ui;

    private ilCtrl $ctrl;

    private ASQServices $asq;

    public function __construct(
        QuestionDto $question_dto,
        UIServices $ui,
        ilCtrl $ctrl,
        ASQServices $asq)
    {
        $this->question_dto = $question_dto;
        $this->ui = $ui;
        $this->ctrl = $ctrl;
        $this->asq = $asq;
    }

    public function executeCommand() : void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_SHOW_FEEDBACK_FORM);
        $this->{$cmd}();
    }

    protected function saveFeedback() : void
    {
        $form = $this->createForm();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_feedback = $form->getFeedbackFromPost();
            $this->question_dto->setFeedback($new_feedback);
            $this->asq->question()->saveQuestion($this->question_dto);
            ilutil::sendSuccess("Question Saved", true);
        }

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }

    protected function showFeedbackForm() : void
    {
        $form = $this->createForm();

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }

    private function createForm() : QuestionFeedbackFormGUI
    {
        return $this->asq->ui()->getQuestionFeedbackForm(
            $this->question_dto,
            $this->ctrl->getFormAction($this, self::CMD_SAVE_FEEDBACK)
        );
    }
}
