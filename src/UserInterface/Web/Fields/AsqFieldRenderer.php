<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields;

use ILIAS\UI\Implementation\Component\Input\Field\Input;
use ILIAS\UI\Implementation\Component\Input\Field\Renderer;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Implementation\Render\TemplateFactory;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use ReflectionProperty;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Fields\DurationInput\DurationInput;

/**
 * Class AsqFieldRenderer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
abstract class AsqFieldRenderer extends AbstractComponentRenderer
{
    use PathHelper;

    protected Component $component;

    public function render(Component $input, RendererInterface $default_renderer) : string
    {
        $this->component = $input;

        $tpl = new ilTemplate("src/UI/templates/default/Input/tpl.context_form.html", true, true);

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

    abstract protected function renderInputField() : string;
}