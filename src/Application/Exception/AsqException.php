<?php
declare(strict_types=1);

namespace srag\asq\Application\Exception;

use ilException;

/**
 * Class AsqException
 *
 * Asq Error Exception
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>

 */
class AsqException extends ilException
{
    public function __construct(string $a_message, int $a_code = 0)
    {
        parent::__construct($a_message, $a_code);
    }
}
