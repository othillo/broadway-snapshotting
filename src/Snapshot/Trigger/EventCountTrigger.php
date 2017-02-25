<?php

declare(strict_types=1);

namespace othillo\Broadway\Snapshotting\Snapshot\Trigger;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use othillo\Broadway\Snapshotting\Snapshot\Trigger;

class EventCountTrigger implements Trigger
{
    /**
     * @var int
     */
    private $eventCount = 100;

    /**
     * @param int $eventCount
     */
    public function __construct(int $eventCount)
    {
        $this->eventCount = $eventCount;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldSnapshot(EventSourcedAggregateRoot $aggregateRoot): bool
    {
        $clonedAggregateRoot = clone $aggregateRoot;

        foreach ($clonedAggregateRoot->getUncommittedEvents() as $domainMessage) {
            if (($domainMessage->getPlayhead() + 1) % $this->eventCount === 0) {
                return true;
            }
        }

        return false;
    }
}
