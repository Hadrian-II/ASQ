<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Matching;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MatchingAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MatchingAnswer extends AbstractValueObject
{

    /**
     * @var string[]
     */
    protected array $matches;

    public function __construct(?array $matches = [])
    {
        $this->matches = $matches;
    }

    public function getMatches() : ?array
    {
        return $this->matches;
    }

    public function getAnswerString() : string
    {
        return is_null($this->matches) ? '' : implode(';', $this->matches);
    }
}
