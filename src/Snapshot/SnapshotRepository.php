<?php

declare(strict_types=1);

namespace othillo\Broadway\Snapshotting\Snapshot;

interface SnapshotRepository
{
    /**
     * @param mixed $id
     *
     * @return Snapshot
     *
     * @throws SnapshotNotFoundException
     */
    public function load($id): Snapshot;

    /**
     * @param Snapshot $snapshot
     */
    public function save(Snapshot $snapshot);
}
