<?php

namespace Brouzie\Components\Indexer\Indexer;

use Brouzie\Components\Indexer\CountableProvider;
use Brouzie\Components\Indexer\IndexationObserver;
use Brouzie\Components\Indexer\IndexationObserver\NullIndexationObserver;
use Brouzie\Components\Indexer\ObservableIndexer;
use Brouzie\Components\Indexer\Persister;
use Brouzie\Components\Indexer\Provider;
use Brouzie\Components\Indexer\Util\BatchUtil;

class BatchedIndexer implements ObservableIndexer
{
    private $provider;

    private $persister;

    private $options;

    private $indexationObserver;

    public function __construct(Provider $provider, Persister $persister, array $options = [])
    {
        $this->provider = $provider;
        $this->persister = $persister;
        $this->options = array_replace(
            [
                'batch_size_read_ids' => 50000,
                'batch_size_read' => 1000,
                'batch_size_write' => 1000,
                'batch_size_delete' => 1000,
            ],
            $options
        );
    }

    public function setIndexationObserver(IndexationObserver $indexationObserver): void
    {
        $this->indexationObserver = $indexationObserver;
    }

    public function reindex(): void
    {
        $indexationObserver = $this->getIndexationObserver();

        if ($this->provider instanceof CountableProvider) {
            $indexationObserver->onCount();
            try {
                $totalIdsCount = $this->provider->getIdsCount();
                $indexationObserver->setTotalIdsCount($totalIdsCount);
            } catch (\BadMethodCallException $e) {

            }
        }

        $indexationObserver->onIndexationStart();
        foreach ($this->provider->getIdsBatches($this->options['batch_size_read_ids']) as $batchNumber => $idsBatch) {
            $this->reindexIds($idsBatch);

            $indexationObserver->onIndexationProgress(count($idsBatch));
        }
    }

    public function reindexIds(array $ids): void
    {
        $i = 0;
        $entries = [];
        foreach (BatchUtil::splitDataPerBatch($ids, $this->options['batch_size_read']) as $idsBatch) {
            foreach ($this->provider->getByIds($idsBatch) as $entry) {
                $entries[] = $entry;
                $i++;

                if (0 === $i % $this->options['batch_size_write']) {
                    $this->persister->persist($entries);
                    $entries = [];
                }
            }
        }

        if ($entries) {
            $this->persister->persist($entries);
        }
    }

    public function update(array $data, array $criteria): void
    {
        // TODO: Implement update() method.
    }

    public function delete(array $ids): void
    {
        foreach (BatchUtil::splitDataPerBatch($ids, $this->options['batch_size_delete']) as $idsBatch) {
            $this->persister->delete($idsBatch);
        }
    }

    public function clear(): void
    {
        $this->getIndexationObserver()->onClear();

        $this->persister->clear();
    }

    private function getIndexationObserver(): IndexationObserver
    {
        if (null === $this->indexationObserver) {
            $this->indexationObserver = new NullIndexationObserver();
        }

        return $this->indexationObserver;
    }
}
