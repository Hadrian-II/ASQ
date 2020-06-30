<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\UserInterface\Web\Form\AbstractObjectFactory;
use srag\asq\Questions\FileUpload\FileUploadScoringConfiguration;
use ilNumberInputGUI;
use ilCheckboxInputGUI;

/**
 * Class FileUploadScoringConfigurationFactory

 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FileUploadScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS = 'fus_points';
    const VAR_COMPLETED_ON_UPLOAD = 'fus_completed_on_upload';

    const CHECKED = 'checked';

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Form\IObjectFactory::getFormfields()
     */
    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = new ilNumberInputGUI($this->language->txt('asq_label_points'), self::VAR_POINTS);
        $points->setRequired(true);
        $points->setSize(2);
        $fields[self::VAR_POINTS] = $points;

        $completed_by_submition = new ilCheckboxInputGUI(
            $this->language->txt('asq_label_completed_by_submition'),
            self::VAR_COMPLETED_ON_UPLOAD
        );
        $completed_by_submition->setInfo($this->language->txt('asq_description_completed_by_submition'));
        $completed_by_submition->setValue(self::CHECKED);
        $fields[self::VAR_COMPLETED_ON_UPLOAD] = $completed_by_submition;

        if ($value !== null) {
            $points->setValue($value->getPoints());
            $completed_by_submition->setChecked($value->isCompletedBySubmition());
        }

        return $fields;
    }

    /**
     * @return FileUploadScoringConfiguration
     */
    public function readObjectFromPost() : AbstractValueObject
    {
        return FileUploadScoringConfiguration::create(
            $this->readFloat(self::VAR_POINTS),
            $this->readString(self::VAR_COMPLETED_ON_UPLOAD) === self::CHECKED
        );
    }

    /**
     * @return FileUploadScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return FileUploadScoringConfiguration::create();
    }
}
