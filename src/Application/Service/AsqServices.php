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
    /**
     * @var QuestionService
     */
    private $question_service;

    /**
     * @var AnswerService
     */
    private $answer_service;

    /**
     * @var UIService
     */
    private $ui_service;

    /**
     * @var LinkService
     */
    private $link_service;

    /**
     * @param QuestionService $question_service
     * @param AnswerService $answer_service
     * @param UIService $ui_service
     * @param LinkService $link_service
     */
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

    /**
     * @return QuestionService
     */
    public function question() : QuestionService
    {
        return $this->question_service;
    }

    /**
     * @return AnswerService
     */
    public function answer() : AnswerService
    {
        return $this->answer_service;
    }

    /**
     * @return UIService
     */
    public function ui(): UIService
    {
        return $this->ui_service;
    }

    /**
     * @return LinkService
     */
    public function link() : LinkService
    {
        return $this->link_service;
    }
}