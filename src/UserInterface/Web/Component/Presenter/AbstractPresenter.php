<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Presenter;

use ILIAS\DI\UIServices;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Abstract Class AbstractPresenter
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractPresenter
{
    /**
     * @var QuestionDto
     */
    protected $question;

    /**
     * @var UIServices
     */
    protected $ui;

    /**
     * AbstractPresenter constructor.
     *
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question, UIServices $ui)
    {
        $this->question = $question;
        $this->ui = $ui;
    }

    /**
     * @return string
     */
    abstract public function generateHtml(AbstractEditor $editor) : string;
}
