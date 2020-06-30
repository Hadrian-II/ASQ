<?php
declare(strict_types=1);

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\asq\Application\Exception;

use ilException;

/**
 * Class AsqException
 *
 * Asq Error Exception
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>

 */
class AsqException extends ilException
{
    /**
     * @param string $a_message
     * @param int $a_code
     */
    public function __construct(string $a_message, int $a_code = 0)
    {
        parent::__construct($a_message, $a_code);
    }
}
