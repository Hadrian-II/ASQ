<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ClozeAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeAnswer extends AbstractValueObject
{
    /**
     * @var ?array
     */
    protected $answers;

    /**
     * @param array $answers
     * @return ClozeAnswer
     */
    public static function create(?array $answers = []) : ClozeAnswer
    {
        $object = new ClozeAnswer();
        $object->answers = $answers;
        return $object;
    }

    /**
     * @return array|NULL
     */
    public function getAnswers() : ?array
    {
        return $this->answers;
    }
}
