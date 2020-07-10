<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset\Scoring\Data;

use srag\asq\Domain\Model\Configuration\AbstractConfiguration;

/**
 * Class TextSubsetScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringConfiguration extends AbstractConfiguration
{
    /**
     * @var ?int
     */
    protected $text_matching;

    /**
     * @param int $text_matching
     * @return TextSubsetScoringConfiguration
     */
    public static function create(?int $text_matching = null) : TextSubsetScoringConfiguration
    {
        $object = new TextSubsetScoringConfiguration();
        $object->text_matching = $text_matching;
        return $object;
    }

    /**
     * @return ?int
     */
    public function getTextMatching() : ?int
    {
        return $this->text_matching;
    }
}
