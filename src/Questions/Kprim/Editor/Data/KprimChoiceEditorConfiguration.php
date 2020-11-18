<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Editor\Data;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceEditorConfiguration extends AbstractValueObject
{
    /**
     * @var ?bool
     */
    protected $shuffle_answers;
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
     * @param int $thumbnail_size
     * @param string $label_true
     * @param string $label_false
     */
    public function __construct(
        ?bool $shuffle_answers = null,
        ?int $thumbnail_size = null,
        ?string $label_true = null,
        ?string $label_false = null
    ) {
        $this->shuffle_answers = $shuffle_answers;
        $this->thumbnail_size = $thumbnail_size;
        $this->label_true = $label_true;
        $this->label_false = $label_false;
    }

    /**
     * @return boolean
     */
    public function isShuffleAnswers() : ?bool
    {
        return $this->shuffle_answers;
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
