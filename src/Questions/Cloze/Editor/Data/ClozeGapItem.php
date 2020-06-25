<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ClozeGapItem
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeGapItem extends AbstractValueObject {
    const VAR_TEXT = 'cgi_text';
    const VAR_POINTS = 'cgi_points';

    /**
     * @var ?string
     */
    protected $text;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param ?string $text
     * @param ?float $points
     * @return ClozeGapItem
     */
    public static function create(?string $text, ?float $points) : ClozeGapItem {
        $item = new ClozeGapItem();
        $item->text = $text;
        $item->points = $points;
        return $item;
    }

    /**
     * @return string
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @return float
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return array
     */
    public function getAsArray() : array {
        return [
            self::VAR_TEXT => $this->text,
            self::VAR_POINTS => $this->points
        ];
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return ! is_null($this->getText()) &&
               ! is_null($this->getPoints());
    }
}