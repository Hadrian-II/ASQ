<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\Application\Service\AuthoringContextContainer;
use srag\asq\UserInterface\Web\Form\QuestionTypeSelectForm;
use srag\asq\Application\Service\ASQServices;

/**
 * Class AsqQuestionCreationGUI
 *
 * Displays question creation Form to choose the type of the new question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqQuestionCreationGUI
{
    const CMD_SHOW_CREATE_FORM = 'showCreationForm';
    const CMD_CREATE_QUESTION = 'createQuestion';
    const CMD_CANCEL_CREATION = 'cancelCreation';

    protected AuthoringContextContainer $contextContainer;

    private ilLanguage $language;

    private UIServices $ui;

    private ilCtrl $ctrl;

    private ASQServices $asq;

    public function __construct(
        AuthoringContextContainer $contextContainer,
        ilLanguage $language,
        UIServices $ui,
        ilCtrl $ctrl,
        ASQServices $asq
    ) {
        $this->contextContainer = $contextContainer;
        $this->language = $language;
        $this->ui = $ui;
        $this->ctrl = $ctrl;
        $this->asq = $asq;
    }

    public function executeCommand() : void
    {
        switch ($this->ctrl->getNextClass()) {
            case strtolower(self::class):
            default:

                $cmd = $this->ctrl->getCmd(self::CMD_SHOW_CREATE_FORM);
                $this->{$cmd}();
        }
    }

    protected function buildCreationForm() : QuestionTypeSelectForm
    {
        $form = new QuestionTypeSelectForm($this->language);
        $form->setFormAction($this->ctrl->getFormAction($this, self::CMD_SHOW_CREATE_FORM));
        $form->addCommandButton(self::CMD_CREATE_QUESTION, $this->language->txt('create'));
        $form->addCommandButton(self::CMD_CANCEL_CREATION, $this->language->txt('cancel'));

        return $form;
    }

    protected function showCreationForm(QuestionTypeSelectForm $form = null) : void
    {
        if ($form === null) {
            $form = $this->buildCreationForm();
        }

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }

    protected function createQuestion() : void
    {
        $form = $this->buildCreationForm();

        if (!$form->checkInput()) {
            $this->showCreationForm($form);
            return;
        }

        $new_question = $this->asq->question()->createQuestion(
            $form->getQuestionType()
        );

        if (!is_null($this->contextContainer->getCaller())) {
            $this->contextContainer->getCaller()->afterQuestionCreated($new_question);
        }

        $this->ctrl->setParameterByClass(
            AsqQuestionConfigEditorGUI::class,
            AsqQuestionAuthoringGUI::VAR_QUESTION_ID,
            $new_question->getId()->toString()
        );

        $this->ctrl->redirectByClass(
            AsqQuestionConfigEditorGUI::class,
            AsqQuestionConfigEditorGUI::CMD_SHOW_FORM
        );
    }

    protected function cancelCreation() : void
    {
        $this->ctrl->redirectToURL(str_replace(
            '&amp;',
            '&',
            $this->contextContainer->getBackLink()->getAction()
        ));
    }
}
