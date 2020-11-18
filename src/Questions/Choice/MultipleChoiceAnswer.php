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
     * @var string[]
     */
    protected $selected_ids;

    /**
     * @param array $selected_ids
     */
    public function __construct(array $selected_ids)
    {
        $this->selected_ids = $selected_ids;
    }

    /**
     * @return array
     */
    public function getSelectedIds() : array
    {
        return $this->selected_ids;
    }
}
