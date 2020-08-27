<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqImageUpload;

use ILIAS\UI\Renderer as RendererInterface;
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
        $tpl->setVariable("INPUT", $this->renderInputField($input));

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
     * @param Component $input
     * @return string
     */
    private function renderInputField(Component $input) : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . "templates/default/tpl.image_upload.html", true, true);

        if (!empty($input->getValue())) {
            $tpl->setCurrentBlock('has_image');
            $tpl->setVariable('NAME', $input->getName());
            $tpl->setVariable('VALUE', $input->getValue());
            $tpl->setVariable('TXT_DELETE', $this->txt("delete_existing_file"));
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('image_upload');
        $tpl->setVariable('NAME', $input->getName());
        $tpl->setVariable('VALUE', $input->getValue());
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    protected function getComponentInterfaceName()
    {
        return [AsqImageUpload::class];
    }
}
