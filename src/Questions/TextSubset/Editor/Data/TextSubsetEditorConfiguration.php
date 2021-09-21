<?php
declare(strict_types = 1);

namespace srag\asq\Questions\TextSubset\Editor\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class TextSubsetEditorConfiguration extends AbstractValueObject
{
    protected ?int $number_of_requested_answers;

    public function __construct(?int $number_of_requested_answers = null)
    {
        $this->number_of_requested_answers = $number_of_requested_answers;
    }

    public function getNumberOfRequestedAnswers() : ?int
    {
        return $this->number_of_requested_answers;
    }
}
