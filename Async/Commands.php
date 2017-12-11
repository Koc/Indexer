<?php

namespace Brouzie\Components\Indexer\Async;

interface Commands
{
    public const REINDEX = 'brouzie_indexer.reindex';

    public const REINDEX_IDS = 'brouzie_indexer.reindex_ids';

    public const UPDATE = 'brouzie_indexer.update';

    public const DELETE = 'brouzie_indexer.delete';

    public const CLEAR = 'brouzie_indexer.clear';
}
