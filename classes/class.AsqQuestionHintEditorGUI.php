<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\Domain\QuestionDto;
use srag\asq\Application\Service\ASQServices;

/**
 * Class AsqQuestionHintEditorGUI
 *
 * GUI for editing Question Hints
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqQuestionHintEditorGUI
{
    const CMD_SAVE = 'save_hint';

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
        $this->ui = $ui;
        $this->question_dto = $question_dto;
        $this->ctrl = $ctrl;
        $this->asq = $asq;
    }

    public function executeCommand() : void
    {
        $this->showHints();
    }

    private function showHints() : void
    {
        $form = $this->asq->ui()->getQuestionHintForm(
            $this->question_dto,
            $this->ctrl->getFormAction($this, self::CMD_SAVE)
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->question_dto->setQuestionHints($form->getHintsFromPost());
            $this->asq->question()->saveQuestion($this->question_dto);
        }

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }
}
