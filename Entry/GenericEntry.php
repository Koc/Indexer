<?php

namespace Brouzie\Components\Indexer\Entry;

use Brouzie\Components\Indexer\Entry;

class GenericEntry implements Entry
{
    private $id;

    private $documentData;

    public function __construct($id, $documentData)
    {
        $this->id = $id;
        $this->documentData = $documentData;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDocumentData()
    {
        return $this->documentData;
    }
}
