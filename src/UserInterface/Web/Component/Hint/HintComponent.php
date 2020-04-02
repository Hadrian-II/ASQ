<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint;

use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\UserInterface\Web\PathHelper;
use ilTemplate;

/**
 * Class FeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintComponent {
    /**
     * @var QuestionHints
     */
    private $hints;
    
    public function __construct(QuestionHints $hints)
    {
        $this->hints = $hints;
    }
    
    public function getHtml() : string
    {        
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.hint.html', true, true);
        
        foreach($this->hints->getHints() as $hint) {
            /** @var $hint QuestionHint */
            $tpl->setCurrentBlock('hint');
            $tpl->setVariable('HINT_CONTENT', $hint->getContent());
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }
}