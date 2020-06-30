<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class AnswerOption
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AnswerOption extends AbstractValueObject
{
    /**
     * @var string
     */
    protected $option_id;
    /**
     * @var ?AnswerDefinition
     */
    protected $display_definition;
    /**
     * @var ?AnswerDefinition
     */
    protected $scoring_definition;

    public static function create(
        string $id,
        ?AbstractValueObject $display_definition = null,
        ?AbstractValueObject $scoring_definition = null
    ) : AnswerOption {
        $object = new AnswerOption();
        $object->option_id = $id;
        $object->display_definition = $display_definition;
        $object->scoring_definition = $scoring_definition;
        return $object;
    }

    /**
     * @return string
     */
    public function getOptionId() : string
    {
        return $this->option_id;
    }

    /**
     * @return AbstractValueObject
     */
    public function getDisplayDefinition()
    {
        return $this->display_definition;
    }

    /**
     * @return AbstractValueObject
     */
    public function getScoringDefinition()
    {
        return $this->scoring_definition;
    }
}
