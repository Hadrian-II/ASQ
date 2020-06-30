<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim;

use srag\asq\Domain\Model\Answer\Answer;

/**
 * Class KprimChoiceEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceAnswer extends Answer
{
    /**
     * @var int[]
     */
    protected $answers;
    
    public static function create(array $answers) : KprimChoiceAnswer
    {
        $object = new KprimChoiceAnswer();
        $object->answers = $answers;
        return $object;
    }
    
    public function getAnswerForId(string $id) : ?bool
    {
        return $this->answers[$id];
    }
}
