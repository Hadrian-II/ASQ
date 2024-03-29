<?php
declare(strict_types = 1);

namespace srag\asq\Questions\FileUpload\Form\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Questions\FileUpload\Scoring\Data\FileUploadScoringConfiguration;
use srag\asq\UserInterface\Web\Form\Factory\AbstractObjectFactory;

/**
 * Class FileUploadScoringConfigurationFactory
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadScoringConfigurationFactory extends AbstractObjectFactory
{
    const VAR_POINTS = 'fus_points';
    const VAR_COMPLETED_ON_UPLOAD = 'fus_completed_on_upload';

    const CHECKED = 'checked';

    public function getFormfields(?AbstractValueObject $value) : array
    {
        $fields = [];

        $points = $this->factory->input()->field()->text($this->language->txt('asq_label_points'));

        $completed_by_submition = $this->factory->input()->field()->checkbox(
            $this->language->txt(
                'asq_label_completed_by_submition',
                $this->language->txt('asq_description_completed_by_submition')
            )
        );

        if ($value !== null) {
            $points = $points->withValue(strval($value->getPoints()));
            $completed_by_submition = $completed_by_submition->withValue($value->isCompletedBySubmition() ?? false);
        }

        $fields[self::VAR_POINTS] = $points;
        $fields[self::VAR_COMPLETED_ON_UPLOAD] = $completed_by_submition;

        return $fields;
    }

    /**
     * @return FileUploadScoringConfiguration
     */
    public function readObjectFromPost(array $postdata) : AbstractValueObject
    {
        return new FileUploadScoringConfiguration(
            $this->readFloat($postdata[self::VAR_POINTS]),
            $postdata[self::VAR_COMPLETED_ON_UPLOAD]
        );
    }

    /**
     * @return FileUploadScoringConfiguration
     */
    public function getDefaultValue() : AbstractValueObject
    {
        return new FileUploadScoringConfiguration();
    }
}
