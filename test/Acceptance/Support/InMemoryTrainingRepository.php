<?php
declare(strict_types=1);

namespace Test\Acceptance\Support;

use Common\EventDispatcher\EventDispatcher;
use DevPro\Domain\Model\Training\Training;
use DevPro\Domain\Model\Training\TrainingId;
use DevPro\Domain\Model\Training\TrainingRepository;
use RuntimeException;

final class InMemoryTrainingRepository implements TrainingRepository
{
    /**
     * @var array & Training[]
     */
    private $entities = [];

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(Training $entity): void
    {
        $this->entities[$entity->trainingId()->asString()] = $entity;

        $this->eventDispatcher->dispatchAll($entity->releaseEvents());
    }

    public function getById(TrainingId $id): Training
    {
        if (!isset($this->entities[$id->asString()])) {
            throw new RuntimeException('Could not find Training with ID ' . $id->asString());
        }

        return $this->entities[$id->asString()];
    }
}
