<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\DurationInput;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Fields\AsqFieldRenderer;

/**
 * Class Renderer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Renderer extends AsqFieldRenderer
{
    protected function renderInputField() : string
    {
        $value = $this->component->getValue();

        if (!is_numeric($value)) {
            $value = null;
        }

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . "templates/default/tpl.DurationInput.html", true, true);

        $tpl->setCurrentBlock('duration');
        $tpl->setVariable('TXT_HOURS', $this->txt('asq_header_hours'));
        $tpl->setVariable('NAME_HOURS', DurationInput::VAR_HOUR . $this->component->getName());
        $tpl->setVariable('VALUE_HOURS', is_null($value) ? "" : strval(floor($value / DurationInput::SECONDS_IN_HOUR)));
        $tpl->setVariable('TXT_MINUTES', $this->txt('asq_header_minutes'));
        $tpl->setVariable('NAME_MINUTES', DurationInput::VAR_MINUTE . $this->component->getName());
        $tpl->setVariable('VALUE_MINUTES', is_null($value) ? "" : strval(floor(($value % DurationInput::SECONDS_IN_HOUR) / DurationInput::SECONDS_IN_MINUTE)));
        $tpl->setVariable('TXT_SECONDS', $this->txt('asq_header_seconds'));
        $tpl->setVariable('NAME_SECONDS', DurationInput::VAR_SECOND . $this->component->getName());
        $tpl->setVariable('VALUE_SECONDS', is_null($value) ? "" : strval($value % DurationInput::SECONDS_IN_MINUTE));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    protected function getComponentInterfaceName() : array
    {
        return [DurationInput::class];
    }
}
