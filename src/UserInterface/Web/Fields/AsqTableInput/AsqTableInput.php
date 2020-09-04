<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\Refinery\Factory;
use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use Closure;
use InvalidArgumentException;
use srag\asq\UserInterface\Web\ImageUploader;

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
    use AsqTablePostTrait;

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
     * @var ImageUploader
     */
    private $uploader;

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
        string $byline = null
    ) {
        if (count($definitions) === 0) {
            throw new InvalidArgumentException("Asq table input needs to have at least one column");
        }

        foreach ($definitions as $definition) {
            if (!get_class($definition) === AsqTableInputFieldDefinition::class) {
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
    public function readValues(InputData $input) : array
    {
        $values = [];
        $i = 0;
        $found = true;
        while ($found) {
            $i += 1;
            $new_value = [];
            $found = false;

            foreach ($this->getDefinitions() as $definition) {
                $item_post_var = $this->getTableItemPostVar($i, $this->getName(), $definition->getPostVar());

                if ($definition->getType() === AsqTableInputFieldDefinition::TYPE_IMAGE) {
                    $uploader = $this->getUploader();

                    $value = $uploader->processImage($item_post_var);
                } else {
                    $value = $input->getOr($item_post_var, null);
                }

                if (!is_null($value)) {
                    $new_value[$definition->getPostVar()] = $value;
                    $found = true;
                }
            }

            if ($found) {
                $values[] = $new_value;
            }
        }

        return $values;
    }

    /**
     * @return ImageUploader
     */
    private function getUploader() : ImageUploader
    {
        if (is_null($this->uploader)) {
            $this->uploader = new ImageUploader();
        }
        return $this->uploader;
    }
}
