<?php

namespace Brouzie\Components\Indexer;

interface IndexationObserver
{
    public function onClear(): void;

    public function onCount(): void;

    public function setTotalIdsCount(int $totalIdsCount): void;

    public function onIndexationStart(): void;

    public function onIndexationProgress(int $incProcessedIdsCount): void;

    public function onIndexationDone(): void;
}
