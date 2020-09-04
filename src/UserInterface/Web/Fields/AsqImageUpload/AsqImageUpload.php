<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqImageUpload;

use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use Closure;
use srag\asq\UserInterface\Web\ImageUploader;

/**
 * Class AsqImageUpload
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqImageUpload extends Input
{
    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Component\Input\Field\Input::isClientSideValueOk()
     */
    protected function isClientSideValueOk($value) : bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Component\Input\Field\Input::getConstraintForRequirement()
     */
    protected function getConstraintForRequirement()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Component\Input\Field\FormInput::getUpdateOnLoadCode()
     */
    public function getUpdateOnLoadCode() : Closure
    {
        return null;
    }

    //TODO stole from base Input
    /**
     * Collects the input, applies trafos on the input and returns
     * a new input reflecting the data that was putted in.
     *
     * @inheritdoc
     */
    public function withInput(InputData $input)
    {
        if ($this->getName() === null) {
            throw new \LogicException("Can only collect if input has a name.");
        }

        //TODO: Discuss, is this correct here. If there is no input contained in this post
        //We assign null. Note that unset checkboxes are not contained in POST.
        if (!$this->isDisabled()) {
            $value = $this->readValue($input);
            // ATTENTION: There was a special case for the Filter Input Container here,
            // which lead to #27909. The issue will most certainly appear again in. If
            // you are the one debugging it and came here: Please don't put knowledge
            // of the special case for the filter in this general class. Have a look
            // into https://mantis.ilias.de/view.php?id=27909 for the according discussion.
            $clone = $this->withValue($value);
        } else {
            $clone = $this;
        }

        $clone->content = $this->applyOperationsTo($clone->getValue());
        if ($clone->content->isError()) {
            return $clone->withError("" . $clone->content->error());
        }

        return $clone;
    }

    /**
     * @return string
     */
    public function readValue(InputData $input) : string
    {
        $image_uploader = new ImageUploader();

        return $image_uploader->processImage($this->getName());
    }
}
