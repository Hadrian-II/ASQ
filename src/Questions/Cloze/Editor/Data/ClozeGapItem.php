<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze\Editor\Data;

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
class ClozeGapItem extends AbstractValueObject
{
    const VAR_TEXT = 'cgi_text';
    const VAR_POINTS = 'cgi_points';

    protected ?string $text;

    protected ?float $points;

    public function __construct(?string $text = null, ?float $points = null)
    {
        $this->text = $text;
        $this->points = $points;
    }

    public function getText() : ?string
    {
        return $this->text;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }

    public function getAsArray() : array
    {
        return [
            self::VAR_TEXT => $this->text,
            self::VAR_POINTS => $this->points
        ];
    }

    public function isComplete() : bool
    {
        return !is_null($this->getText()) &&
               !is_null($this->getPoints());
    }
}
