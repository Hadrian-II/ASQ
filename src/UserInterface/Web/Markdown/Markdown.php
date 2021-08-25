<?php

namespace srag\asq\UserInterface\Web\Markdown;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\JavaScriptBindable;

/**
 * Class Markdown
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Markdown
{
    use ComponentHelper;
    use JavaScriptBindable;

    private string $content;

    public function __construct($content)
    {
        $this->checkStringArg("content", $content);

        $this->content = $content;

        $this->on_load_code_binder = function($id) {
            return "il.UI.markup.initiateMarkup($id);";
        };
    }

    public function getContent() : string
    {
        return $this->content;
    }

    public function withContent($content)
    {
        $this->checkStringArg("content", $content);

        $clone = clone $this;
        $clone->content = $content;
        return $clone;
    }
}
