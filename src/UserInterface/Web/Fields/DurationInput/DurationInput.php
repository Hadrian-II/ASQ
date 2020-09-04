<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\DurationInput;

use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use Closure;

/**
 * Class DurationInput
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class DurationInput extends Input
{
    const VAR_HOUR = 'hour';
    const VAR_MINUTE = 'minute';
    const VAR_SECOND = 'second';
    const SECONDS_IN_HOUR = 3600;
    const SECONDS_IN_MINUTE = 60;

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
            $value = $this->readValues($input);
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
     * @return array
     */
    public function readValues(InputData $input) : ?int
    {
        $value = 0;

        $second_name = self::VAR_SECOND . $this->getName();
        $minute_name = self::VAR_MINUTE . $this->getName();
        $hour_name = self::VAR_HOUR . $this->getName();

        if (!is_numeric($input->get($second_name)) ||
            !is_numeric($input->get($minute_name)) ||
            !is_numeric($input->get($hour_name))) {
            return null;
        }

        $value += $input->get($second_name);
        $value += $input->get($minute_name) * self::SECONDS_IN_MINUTE;
        $value += $input->get($hour_name) * self::SECONDS_IN_HOUR;

        return $value;
    }

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
}
