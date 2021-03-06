<?php

declare(strict_types=1);

namespace othillo\Broadway\Snapshotting\EventSourcing;

use Broadway\Domain\AggregateRoot;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Broadway\Repository\Repository;
use othillo\Broadway\Snapshotting\Snapshot\Snapshot;
use othillo\Broadway\Snapshotting\Snapshot\SnapshotNotFoundException;
use othillo\Broadway\Snapshotting\Snapshot\SnapshotRepository;
use othillo\Broadway\Snapshotting\Snapshot\Trigger;

class SnapshottingEventSourcingRepository implements Repository
{
    private $eventSourcingRepository;
    private $eventStore;
    private $snapshotRepository;
    private $trigger;

    public function __construct(
        EventSourcingRepository $eventSourcingRepository,
        EventStore $eventStore,
        SnapshotRepository $snapshotRepository,
        Trigger $trigger
    ) {
        $this->eventSourcingRepository = $eventSourcingRepository;
        $this->eventStore              = $eventStore;
        $this->snapshotRepository      = $snapshotRepository;
        $this->trigger                 = $trigger;
    }

    /**
     * {@inheritdoc}
     */
    public function load($id): AggregateRoot
    {
        try {
            $snapshot = $this->snapshotRepository->load($id);
        } catch (SnapshotNotFoundException $exception) {
            return $this->eventSourcingRepository->load($id);
        }

        $aggregateRoot = $snapshot->getAggregateRoot();
        $aggregateRoot->initializeState(
            $this->eventStore->loadFromPlayhead($id, $snapshot->getPlayhead())
        );

        return $aggregateRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AggregateRoot $aggregate)
    {
        $takeSnaphot = $this->trigger->shouldSnapshot($aggregate);

        $this->eventSourcingRepository->save($aggregate);

        if ($takeSnaphot) {
            $this->snapshotRepository->save(
                new Snapshot($aggregate)
            );
        }
    }
}
