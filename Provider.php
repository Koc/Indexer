<?php

namespace Brouzie\Components\Indexer;

interface Provider
{
    /**
     * @return iterable|array<int, array<int, mixed>>
     */
    public function getIdsBatches(int $batchSize): iterable;

    /**
     * @return iterable|object[]
     */
    public function getByIds(array $ids): iterable;
}
