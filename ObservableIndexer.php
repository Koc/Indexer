<?php

namespace Brouzie\Components\Indexer;

interface ObservableIndexer extends Indexer
{
    public function setIndexationObserver(IndexationObserver $indexationObserver): void;
}
