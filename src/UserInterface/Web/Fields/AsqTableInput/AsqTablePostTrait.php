<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

/**
 * Trait AsqTablePostTrait
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
trait AsqTablePostTrait
{
    private function getTableItemPostVar(int $id, string $name, string $definition_postvar) : string
    {
        return sprintf('%s_%s_%s', $id, $name, $definition_postvar);
    }
}
