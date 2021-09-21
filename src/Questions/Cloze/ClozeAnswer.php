<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ClozeAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeAnswer extends AbstractValueObject
{
    protected ?array $answers;

    public function __construct(?array $answers = [])
    {
        $this->answers = $answers;
    }

    public function getAnswers() : ?array
    {
        return $this->answers;
    }
}
