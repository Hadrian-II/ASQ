<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceAnswer extends AbstractValueObject
{
    /**
     * @var int[]
     */
    protected array $answers;

    public function __construct(array $answers = [])
    {
        $this->answers = $answers;
    }

    public function getAnswerForId(string $id) : ?bool
    {
        return $this->answers[$id];
    }
}
