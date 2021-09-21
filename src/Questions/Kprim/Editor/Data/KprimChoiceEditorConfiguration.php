<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Editor\Data;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;

/**
 * Class KprimChoiceEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceEditorConfiguration extends AbstractValueObject
{
    protected ?bool $shuffle_answers;

    protected ?int $thumbnail_size;

    protected ?string $label_true;

    protected ?string $label_false;

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

    public function isShuffleAnswers() : ?bool
    {
        return $this->shuffle_answers;
    }

    public function getThumbnailSize() : ?int
    {
        return $this->thumbnail_size;
    }

    public function getLabelTrue() : ?string
    {
        return $this->label_true;
    }

    public function getLabelFalse() : ?string
    {
        return $this->label_false;
    }
}
