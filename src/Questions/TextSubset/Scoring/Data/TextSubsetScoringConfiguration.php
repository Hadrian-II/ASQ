<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Scoring\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class TextSubsetScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringConfiguration extends AbstractValueObject
{
    protected ?int $text_matching;

    public function __construct(?int $text_matching = null)
    {
        $this->text_matching = $text_matching;
    }

    public function getTextMatching() : ?int
    {
        return $this->text_matching;
    }
}
