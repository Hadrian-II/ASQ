<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class AnswerOptions
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AnswerOptions extends AbstractValueObject
{

    /**
     * @var array
     */
    protected $options;

    public static function create(array $options) : AnswerOptions
    {
        $object = new AnswerOptions();
        $object->options = $options;
        return $object;
    }

    /**
     * @return AnswerOption[]
     */
    public function getOptions() : ?array
    {
        return $this->options;
    }
}
