<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Cloze;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ClozeGapConfiguration
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class ClozeGapConfiguration extends AbstractValueObject
{
    const DEFAULT_FIELD_LENGTH = 80;

    const TYPE_TEXT = 'clz_text';

    const TYPE_NUMBER = 'clz_number';

    const TYPE_DROPDOWN = 'clz_dropdown';

    abstract function getMaxPoints() : ?float;

    abstract function isComplete() : bool;
}