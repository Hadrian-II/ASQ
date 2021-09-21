<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Scoring;

use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Questions\FileUpload\Scoring\Data\FileUploadScoringConfiguration;

/**
 * Class FileUploadScoring
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadScoring extends AbstractScoring
{
    protected FileUploadScoringConfiguration $configuration;

    public function __construct($question)
    {
        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();
    }

    public function score(AbstractValueObject $answer) : float
    {
        $reached_points = 0;

        if ($this->configuration->isCompletedBySubmition()) {
            if ($answer->getFiles() !== null && count($answer->getFiles()) > 0) {
                $reached_points = $this->getMaxScore();
            }
        } else {
            throw new AsqException('Cant automatically score Fileupload');
        }

        return $reached_points;
    }

    protected function calculateMaxScore() : float
    {
        return $this->configuration->getPoints();
    }

    public function getBestAnswer() : AbstractValueObject
    {
        throw new AsqException(self::BEST_ANSWER_CREATION_IMPOSSIBLE_ERROR);
    }

    public function isComplete() : bool
    {
        return ! is_null($this->configuration->getPoints());
    }
}
