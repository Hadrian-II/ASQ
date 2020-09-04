<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\AssessmentQuestion\Test;

use ilLogger;
use Monolog\Logger;

/**
 * Class NullLogger
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NullLogger extends ilLogger
{
    public function __construct()
    {
        parent::__construct(new Logger('blah'));
    }

    public function lang()
    {
        return new Logger('blah');
    }

    public function cal()
    {
        return new Logger('cal');
    }
}
