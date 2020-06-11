<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class KprimChoiceEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceEditorConfiguration extends AbstractConfiguration {
    /**
     * @var ?bool
     */
    protected $shuffle_answers;
    /**
     * @var ?bool
     */
    protected $single_line;
    /**
     * @var ?int
     */
    protected $thumbnail_size;
    /**
     * @var ?string
     */
    protected $label_true;
    /**
     * @var ?string
     */
    protected $label_false;

    /**
     * @param bool $shuffle_answers
     * @param bool $single_line
     * @param int $thumbnail_size
     * @param string $label_true
     * @param string $label_false
     * @return KprimChoiceEditorConfiguration
     */
    static function create(?bool $shuffle_answers,
                           ?bool $single_line,
                           ?int $thumbnail_size,
                           ?string $label_true,
                           ?string $label_false) : KprimChoiceEditorConfiguration
        {
            $object = new KprimChoiceEditorConfiguration();
            $object->single_line = $single_line;
            $object->shuffle_answers = $shuffle_answers;
            $object->thumbnail_size = $thumbnail_size;
            $object->label_true = $label_true;
            $object->label_false = $label_false;

            return $object;
    }

    /**
     * @return boolean
     */
    public function isShuffleAnswers() : ?bool
    {
        return $this->shuffle_answers;
    }

    /**
     * @return boolean
     */
    public function isSingleLine() : ?bool
    {
        return $this->single_line;
    }

    /**
     * @return number
     */
    public function getThumbnailSize() : ?int
    {
        return $this->thumbnail_size;
    }

    /**
     * @return string
     */
    public function getLabelTrue() : ?string
    {
        return $this->label_true;
    }

    /**
     * @return string
     */
    public function getLabelFalse() : ?string
    {
        return $this->label_false;
    }
}