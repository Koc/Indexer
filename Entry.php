<?php

namespace Brouzie\Components\Indexer;

interface Entry
{
    public function getId();

    public function getDocumentData();
}
