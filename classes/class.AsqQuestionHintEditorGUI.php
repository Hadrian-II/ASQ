<?php
declare(strict_types=1);

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Hint\Form\HintFormGUI;
use srag\asq\AsqGateway;
use ILIAS\DI\UIServices;

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
    const CMD_SAVE = 'save';

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
     * @param QuestionDto $question_dto
     * @param ilLanguage $language
     * @param UIServices $ui
     */
    public function __construct(QuestionDto $question_dto, ilLanguage $language, UIServices $ui)
    {
        $this->language = $language;
        $this->ui = $ui;
        $this->question_dto = $question_dto;
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
        $form = new HintFormGUI($this->question_dto, $this->ui, $this->language);
        $form->addCommandButton(self::CMD_SAVE, $this->language->txt('save'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->question_dto->setQuestionHints($form->getHintsFromPost());
            AsqGateway::get()->question()->saveQuestion($this->question_dto);
        }

        $this->ui->mainTemplate()->setContent($form->getHTML());
    }
}
