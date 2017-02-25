<?php

declare(strict_types=1);

namespace othillo\Broadway\Snapshotting\EventStore;

use Broadway\Domain\DomainEventStreamInterface;
use Broadway\EventStore\EventStoreInterface;

interface SnapshottingEventStoreInterface extends EventStoreInterface
{
    /**
     * @param mixed $id
     * @param int   $playhead
     *
     * @return DomainEventStreamInterface
     */
    public function loadFromPlayhead($id, $playhead): DomainEventStreamInterface;
}
