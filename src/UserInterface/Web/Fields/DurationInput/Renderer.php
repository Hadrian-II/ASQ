<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\DurationInput;

use ILIAS\UI\Renderer as RendererInterface;

;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\PathHelper;

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

    /**
     * @var DurationInput
     */
    private $component;

    //TODO stole method from Input/Field/Renderer, see to integrate this into input field renderer
    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Render\ComponentRenderer::render()
     */
    public function render(Component $input, RendererInterface $default_renderer) : string
    {
        $this->component = $input;

        $tpl = new ilTemplate("src/UI/templates/default/Input/tpl.context_form.html", true, true);
        /**
         * TODO: should we throw an error in case for no name or render without name?
         *
         * if(!$input->getName()){
         * throw new \LogicException("Cannot render '".get_class($input)."' no input name given.
         * Is there a name source attached (is this input packed into a container attaching
         * a name source)?");
         * } */
        if ($input->getName()) {
            $tpl->setVariable("NAME", $input->getName());
        } else {
            $tpl->setVariable("NAME", "");
        }

        $tpl->setVariable("LABEL", $input->getLabel());
        $tpl->setVariable("INPUT", $this->renderInputField());

        if ($input->getByline() !== null) {
            $tpl->setCurrentBlock("byline");
            $tpl->setVariable("BYLINE", $input->getByline());
            $tpl->parseCurrentBlock();
        }

        if ($input->isRequired()) {
            $tpl->touchBlock("required");
        }

        if ($input->getError() !== null) {
            $tpl->setCurrentBlock("error");
            $tpl->setVariable("ERROR", $input->getError());
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    /**
     * @param string $a_mode
     *
     * @return string
     * @throws \ilTemplateException
     */
    private function renderInputField() : string
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

    protected function getComponentInterfaceName()
    {
        return [DurationInput::class];
    }
}
