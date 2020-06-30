<?php
declare(strict_types=1);

namespace srag\asq;

use srag\asq\Application\Service\AnswerService;
use srag\asq\Application\Service\LinkService;
use srag\asq\Application\Service\QuestionService;
use srag\asq\Application\Service\UIService;
use srag\asq\Statistics\StatisticsService;

/**
 * Class AsqGateway
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqGateway
{
    /**
     * @var AsqGateway
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * @return AsqGateway
     */
    public static function get() : AsqGateway
    {
        if (is_null(self::$instance)) {
            self::$instance = new AsqGateway();
        }

        return self::$instance;
    }

    /**
     * @var QuestionService
     */
    private $question_service;

    /**
     * @return QuestionService
     */
    public function question() : QuestionService
    {
        if (is_null($this->question_service)) {
            $this->question_service = new QuestionService();
        }

        return $this->question_service;
    }

    /**
     * @var AnswerService
     */
    private $answer_service;

    /**
     * @return AnswerService
     */
    public function answer() : AnswerService
    {
        if (is_null($this->answer_service)) {
            $this->answer_service = new AnswerService();
        }

        return $this->answer_service;
    }

    /**
     * @var LinkService
     */
    private $link_service;

    /**
     * @return LinkService
     */
    public function link() : LinkService
    {
        if (is_null($this->link_service)) {
            $this->link_service = new LinkService();
        }

        return $this->link_service;
    }

    /**
     * @var UIService
     */
    private $ui_service;

    /**
     * @return UIService
     */
    public function ui() : UIService
    {
        if (is_null($this->ui_service)) {
            $this->ui_service = new UIService();
        }

        return $this->ui_service;
    }

    /**
     * @var StatisticsService
     */
    private $statistics_service;

    public function statistics() : StatisticsService
    {
        if (is_null($this->statistics_service)) {
            $this->statistics_service = new StatisticsService();
        }

        return $this->statistics_service;
    }
}
