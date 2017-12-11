<?php

namespace Brouzie\Components\Indexer\IndexationObserver;

use Brouzie\Components\Indexer\IndexationObserver;

class NullIndexationObserver implements IndexationObserver
{
    public function onClear(): void
    {
    }

    public function onCount(): void
    {
    }

    public function setTotalIdsCount(int $totalIdsCount): void
    {
    }

    public function onIndexationStart(): void
    {
    }

    public function onIndexationProgress(int $incProcessedIdsCount): void
    {
    }

    public function onIndexationDone(): void
    {
    }
}
