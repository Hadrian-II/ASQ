<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Fields\AsqTableInput;

/**
 * Trait AsqTablePostTrait
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
trait AsqTablePostTrait
{
    /**
     * @param int $id
     * @param string $postvar
     * @param string $definition_postvar
     * @return string
     */
    private function getTableItemPostVar(int $id, string $name, string $definition_postvar) : string
    {
        return sprintf('%s_%s_%s', $id, $name, $definition_postvar);
    }
}
