<?php
declare(strict_types = 1);

namespace srag\asq\Questions\ImageMap\Form;

use srag\asq\UserInterface\Web\Form\QuestionFormFactory;
use srag\asq\Questions\MultipleChoice\Form\MultipleChoiceScoringConfigurationFactory;
use srag\asq\Questions\MultipleChoice\Form\MultipleChoiceScoringDefinitionFactory;
use srag\asq\UserInterface\Web\PathHelper;

/**
 * Class ImageMapFormFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapFormFactory extends QuestionFormFactory
{
    use PathHelper;

    public function __construct()
    {
        global $DIC;

        parent::__construct(
            new ImageMapEditorConfigurationFactory($DIC->language()),
            new MultipleChoiceScoringConfigurationFactory($DIC->language()),
            new ImageMapEditorDefinitionFactory($DIC->language()),
            new MultipleChoiceScoringDefinitionFactory($DIC->language()));
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormFactory::getScripts()
     */
    public function getScripts() : array
    {
        return [ $this->getBasePath(__DIR__) . 'src/Questions/ImageMap/ImageMapAuthoring.js' ];
    }
}