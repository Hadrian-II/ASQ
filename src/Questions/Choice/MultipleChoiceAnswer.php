<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class MultipleChoiceAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceAnswer extends AbstractValueObject
{
    /**
     * @var string[]
     */
    protected array $selected_ids;

    public function __construct(array $selected_ids = [])
    {
        $this->selected_ids = $selected_ids;
    }

    public function getSelectedIds() : array
    {
        return $this->selected_ids;
    }
}
