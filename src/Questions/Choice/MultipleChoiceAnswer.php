<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceAnswer extends AbstractValueObject
{
    /**
     * @var int[]
     */
    protected $selected_ids;

    public static function create(array $selected_ids) : MultipleChoiceAnswer
    {
        $object = new MultipleChoiceAnswer();
        $object->selected_ids = $selected_ids;
        return $object;
    }

    public function getSelectedIds() : array
    {
        return $this->selected_ids;
    }
}
