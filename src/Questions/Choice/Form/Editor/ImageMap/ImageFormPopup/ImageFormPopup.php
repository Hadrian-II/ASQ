<?php
declare(strict_types=1);

namespace srag\asq\Questions\Choice\Form\Editor\ImageMap\ImageFormPopup;

use ILIAS\UI\Implementation\Component\Input\Field\Input;
use Closure;

/**
 * Class ImageFormPopup
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class ImageFormPopup extends Input
{
    protected function isClientSideValueOk($value) : bool
    {
        return true;
    }

    protected function getConstraintForRequirement()
    {
        return null;
    }

    public function getUpdateOnLoadCode() : Closure
    {
        return function() {};
    }
}
