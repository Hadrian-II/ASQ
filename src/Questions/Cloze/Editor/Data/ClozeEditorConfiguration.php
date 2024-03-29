<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ClozeEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ClozeEditorConfiguration extends AbstractValueObject
{
    protected ?string $cloze_text;

    /**
     * @var ?ClozeGapConfiguration[]
     */
    protected ?array $gaps = [];

    public function __construct(?string $cloze_text = null, ?array $gaps = null)
    {
        $this->cloze_text = $cloze_text;
        $this->gaps = $gaps;
    }

    public function getClozeText() : ?string
    {
        return $this->cloze_text;
    }

    public function getGaps() : ?array
    {
        return $this->gaps;
    }
}
