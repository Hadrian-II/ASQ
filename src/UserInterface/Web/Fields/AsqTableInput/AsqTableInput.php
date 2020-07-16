<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\Refinery\Factory;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use Closure;
use InvalidArgumentException;

/**
 * Class AsqTableInput
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqTableInput extends Input
{
    const OPTION_ORDER = 'TableInputOrder';
    const OPTION_HIDE_ADD_REMOVE = 'TableInputHideAddRemove';
    const OPTION_HIDE_EMPTY = 'TableInputHideEmpty';
    const OPTION_MIN_ROWS = 'TableInputMinRows';
    const DEFAULT_MIN_ROWS = 1;

    /**
     * @var AsqTableInputFieldDefinition[]
     */
    private $definitions;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $label
     * @param array $definitions
     * @param string $byline
     */
    public function __construct(
        string $label,
        array $definitions,
        DataFactory $data_factory,
        Factory $factory,
        string $byline = null)
    {
        if (count($definitions) === 0)  {
            throw new InvalidArgumentException("Asq table input needs to have at least one column");
        }

        foreach($definitions as $definition) {
            if (! get_class($definition) === AsqTableInputFieldDefinition::class) {
                throw new InvalidArgumentException("Asq table input column definition need to be of type AsqTableInputFieldDefinition");
            }
        }

        $this->definitions = $definitions;
        $this->options = [];
        parent::__construct($data_factory, $factory, $label, $byline);
    }

    /**
     * @param array $options
     * @return AsqTableInput
     */
    public function withOptions(array $options) : AsqTableInput
    {
        $clone = clone $this;
        $clone->options = $options;

        return $clone;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @return AsqTableInputFieldDefinition[]
     */
    public function getDefinitions() : array
    {
        return $this->definitions;
    }

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Component\Input\Field\Input::isClientSideValueOk()
     */
    protected function isClientSideValueOk($value): bool
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
    public function getUpdateOnLoadCode(): Closure
    {
        return null;
    }
}
