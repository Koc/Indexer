<?php

namespace Brouzie\Components\Indexer;

interface CountableProvider extends Provider
{
    public function getIdsCount(): int;
}
