<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint;

use ilTemplate;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class FeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class HintComponent
{
    use PathHelper;

    private QuestionHints $hints;

    public function __construct(QuestionHints $hints)
    {
        $this->hints = $hints;
    }

    public function getHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.hint.html', true, true);

        foreach ($this->hints->getHints() as $hint) {
            /** @var $hint QuestionHint */
            $tpl->setCurrentBlock('hint');
            $tpl->setVariable('HINT_CONTENT', $hint->getContent());
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }
}
