<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceAnswer extends AbstractValueObject
{
    /**
     * @var int[]
     */
    protected $answers;

    /**
     * @param array $answers
     */
    public function __construct(array $answers = [])
    {
        $this->answers = $answers;
    }

    /**
     * @param string $id
     * @return bool|NULL
     */
    public function getAnswerForId(string $id) : ?bool
    {
        return $this->answers[$id];
    }
}
