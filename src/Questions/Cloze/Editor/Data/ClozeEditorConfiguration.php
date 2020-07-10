<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

use srag\asq\Domain\Model\Configuration\AbstractConfiguration;

/**
 * Class ClozeEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?string
     */
    protected $cloze_text;

    /**
     * @var ?ClozeGapConfiguration[]
     */
    protected $gaps = [];

    /**
     * @param string $cloze_text
     * @param array $gaps
     * @return ClozeEditorConfiguration
     */
    public static function create(string $cloze_text, array $gaps) : ClozeEditorConfiguration
    {
        $config = new ClozeEditorConfiguration();
        $config->cloze_text = $cloze_text;
        $config->gaps = $gaps;
        return $config;
    }

    /**
     * @return string
     */
    public function getClozeText() : ?string
    {
        return $this->cloze_text;
    }

    /**
     * @return ClozeGapConfiguration[]
     */
    public function getGaps() : ?array
    {
        return $this->gaps;
    }
}
