<?php

namespace Brouzie\Components\Indexer\Util;

class BatchUtil
{
    public static function splitDataPerBatch(array $data, int $batchSize): iterable
    {
        $i = 0;
        while (!empty($data)) {
            yield $i => array_splice($data, 0, $batchSize);
            $i++;
        }
    }
}
