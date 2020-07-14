<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Presenter;

use ilTemplate;
use srag\asq\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class DefaultPresenter
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class DefaultPresenter extends AbstractPresenter
{
    use PathHelper;

    /**
     * @return string
     * @throws \ilTemplateException
     */
    public function generateHtml(AbstractEditor $editor, bool $show_feedback = false) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.DefaultPresenter.html', true, true);

        $tpl->setCurrentBlock('question');
        $tpl->setVariable('QUESTIONTEXT', $this->question->getData()->getQuestionText());
        $tpl->setVariable('EDITOR', $editor->generateHtml($show_feedback));
        $tpl->parseCurrentBlock();

        $this->ui->mainTemplate()->addCss($this->getBasePath(__DIR__) . 'css/asq.css');

        return $tpl->get();
    }
}
