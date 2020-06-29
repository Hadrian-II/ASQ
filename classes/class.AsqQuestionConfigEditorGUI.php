<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\AsqGateway;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Application\Service\AuthoringContextContainer;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class AsqQuestionConfigEditorGUI
 *
 * Displays Question configuration Form used to edit Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqQuestionConfigEditorGUI
{
    use PostAccess;

    const CMD_SHOW_FORM = 'showForm';
    const CMD_SAVE_FORM = 'saveForm';
    const CMD_SAVE_AND_RETURN = 'saveAndReturn';

    /**
     * @var AuthoringContextContainer
     */
    protected $contextContainer;

    /**
     * @var QuestionDto
     */
    private $question;

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
     * @param AuthoringContextContainer $contextContainer
     * @param string $questionId
     * @param ilLanguage $language
     * @param UIServices $ui
     * @param ilCtrl $ctrl
     */
    public function __construct(
        AuthoringContextContainer $contextContainer,
        string $questionId,
        ilLanguage $language,
        UIServices $ui,
        ilCtrl $ctrl)
    {
        $this->contextContainer = $contextContainer;
        $this->question = AsqGateway::get()->question()->getQuestionByQuestionId($questionId);
        $this->language = $language;
        $this->ui = $ui;
        $this->ctrl = $ctrl;
    }


    public function executeCommand() : void
    {
        switch( $this->ctrl->getNextClass() )
        {
            case strtolower(self::class):
            default:

                $cmd = $this->ctrl->getCmd(self::CMD_SHOW_FORM);
                $this->{$cmd}();
        }
    }


    /**
     * @param ilPropertyFormGUI|null $form
     * @throws Exception
     */
    protected function showForm(ilPropertyFormGUI $form = null) : void
    {
        if( $form === null )
        {
            $form = $this->buildForm();
        }

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }


    /**
     * @throws Exception
     */
    protected function saveForm() : void
    {
        $form = $this->buildForm();

        $this->saveQuestion($form);

        ilutil::sendInfo("Question Saved", true);

        $form->checkInput();
        $this->showForm($form);
    }

    /**
     * @throws Exception
     */
    protected function saveAndReturn() : void
    {
        $form = $this->buildForm();

        $this->saveQuestion($form);

        if( !$form->checkInput() )
        {
            $this->showForm($form);
            return;
        }

        $this->ctrl->redirectToUrl(str_replace('&amp;', '&',
            $this->contextContainer->getBackLink()->getAction()
        ));
    }

    private function saveQuestion(QuestionFormGUI $form) : void
    {
        $changes = $form->getQuestion();
        $this->question->setData($changes->getData());
        $this->question->setPlayConfiguration($changes->getPlayConfiguration());
        $this->question->setAnswerOptions($changes->getAnswerOptions());
        AsqGateway::get()->question()->saveQuestion($this->question);
    }

    /**
     * @return QuestionFormGUI
     * @throws Exception
     */
    private function buildForm() : QuestionFormGUI
    {
        $form = AsqGateway::get()->ui()->getQuestionEditForm($this->question);
        $form->setFormAction($this->ctrl->getFormAction($this, self::CMD_SHOW_FORM));
        $form->addCommandButton(self::CMD_SAVE_AND_RETURN, $this->language->txt('save_return'));
        $form->addCommandButton(self::CMD_SAVE_FORM, $this->language->txt('save'));

        return $form;
    }

    private function createRevision() : void
    {
        $form = $this->buildForm();

        $rev_name = $this->getPostValue(QuestionFormGUI::VAR_REVISION_NAME);

        if (empty($rev_name)) {
            ilutil::sendInfo($this->language->txt('asq_missing_revision_name'));
        } else {
            try {
                AsqGateway::get()->question()->createQuestionRevision($rev_name, $this->question->getId());
                ilUtil::sendSuccess($this->language->txt('asq_revision_created'));
            } catch(AsqException $e) {
                ilutil::sendFailure($e->getMessage());
            }
        }

        $this->showForm($form);
    }
}
