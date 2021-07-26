<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

/**
 * Class ASQServices
 *
 * Main node that contains all AsqServices
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqServices
{
    private QuestionService $question_service;

    private AnswerService $answer_service;

    private UIService $ui_service;

    private LinkService $link_service;

    public function __construct(
        QuestionService $question_service,
        AnswerService $answer_service,
        UIService $ui_service,
        LinkService $link_service)
    {
        $this->question_service = $question_service;
        $this->answer_service = $answer_service;
        $this->ui_service = $ui_service;
        $this->link_service = $link_service;
    }

    public function question() : QuestionService
    {
        return $this->question_service;
    }

    public function answer() : AnswerService
    {
        return $this->answer_service;
    }

    public function ui(): UIService
    {
        return $this->ui_service;
    }

    public function link() : LinkService
    {
        return $this->link_service;
    }
}