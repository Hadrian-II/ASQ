<?php
declare(strict_types=1);

namespace srag\asq\Questions\MultipleChoice;


use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class MultipleChoiceScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringConfiguration extends AbstractConfiguration {
    public static function create() : MultipleChoiceScoringConfiguration {
        return new MultipleChoiceScoringConfiguration();
    }
}