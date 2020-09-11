<?php
declare(strict_types=1);

use ILIAS\DI\UIServices;
use srag\asq\Application\Exception\AsqException;
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
     * @var ASQServices
     */
    private $asq;

    /**
     * @param QuestionDto $question_dto
     * @param ilLanguage $language
     * @param UIServices $ui
     * @param ilCtrl $ctrl
     * @param ASQServices $asq
     */
    public function __construct(
        QuestionDto $question_dto,
        ilLanguage $language,
        UIServices $ui,
        ilCtrl $ctrl,
        ASQServices $asq)
    {
        $this->language = $language;
        $this->ui = $ui;
        $this->question_dto = $question_dto;
        $this->ctrl = $ctrl;
        $this->asq = $asq;
    }


    /**
     * @throws AsqException
     */
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
