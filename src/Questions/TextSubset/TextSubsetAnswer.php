<?php
declare(strict_types = 1);
namespace srag\asq\Questions\TextSubset;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetAnswer extends AbstractValueObject
{

    /**
     * @var ?int[]
     */
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
