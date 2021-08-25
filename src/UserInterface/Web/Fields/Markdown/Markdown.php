<?php

/* Copyright (c) 2021 Adrian Lüthi <adi.l@bluewin.ch> Extended GPL, see docs/LICENSE */

namespace srag\asq\UserInterface\Web\Fields\Markdown;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\UI\Implementation\Component\Input\Field\Input;

/**
 * Class Markdown
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Markdown extends Input
{
    public function __construct(
        DataFactory $data_factory,
        \ILIAS\Refinery\Factory $refinery,
        $label,
        $byline
        ) {
            parent::__construct($data_factory, $refinery, $label, $byline);

            $this->setAdditionalTransformation(
                $refinery->string()->stripTags()
            );

            $this->on_load_code_binder = function($id) {
                return "il.UI.Input.Markdown.initiateEditor($id);";
            };
    }

    protected function isClientSideValueOk($value) : bool
    {
        if (! is_string($value)) {
            return false;
        }

        return true;
    }

    protected function getConstraintForRequirement()
    {
        return $this->refinery->string()->hasMinLength(1);
    }

    public function getUpdateOnLoadCode() : \Closure
    {
        // TODO whatever this is
        return function ($id) {
            return "";
            $code = "$('#$id').on('input', function(event) {
				il.UI.input.onFieldUpdate(event, '$id', $('#$id').val());
			});
			il.UI.input.onFieldUpdate(event, '$id', $('#$id').val());";
            return $code;
        };
    }

    public function isComplex()
    {
        return false;
    }
}
