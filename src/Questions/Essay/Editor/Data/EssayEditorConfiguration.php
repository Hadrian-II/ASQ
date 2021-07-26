<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class EssayEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class EssayEditorConfiguration extends AbstractValueObject
{
    protected ?int $max_length;

    public function __construct(?int $max_length = null)
    {
        $this->max_length = $max_length;
    }

    public function getMaxLength() : ?int
    {
        return $this->max_length;
    }
}
