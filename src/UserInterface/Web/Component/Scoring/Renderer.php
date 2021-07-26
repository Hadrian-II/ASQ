<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Scoring;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\Infrastructure\Helpers\PathHelper;

/**
 * Class Renderer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Renderer extends AbstractComponentRenderer
{
    use PathHelper;

    public function render(Component $component, RendererInterface $default_renderer) : string
    {
        $scoring_class = $component->getQuestion()->getType()->getScoringClass();
        $scoring = new $scoring_class($component->getQuestion());

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.answer_scoring.html', true, true);

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable(
            'ANSWER_SCORE',
            sprintf(
                $this->txt('asq_you_received_a_of_b_points'),
                $scoring->score($component->getAnswer()),
                $scoring->getMaxScore()
            )
        );
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    protected function getComponentInterfaceName() : array
    {
        return [
            ScoringComponent::class,
        ];
    }
}
