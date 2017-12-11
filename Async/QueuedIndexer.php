<?php

namespace Brouzie\Components\Indexer\Async;

use Brouzie\Components\Indexer\Async\Message\IdsMessage;
use Brouzie\Components\Indexer\Indexer;
use Enqueue\Client\ProducerInterface;

class QueuedIndexer implements Indexer
{
    private $producer;

    private $indexer;

    public function __construct(ProducerInterface $producer, Indexer $indexer = null)
    {
        $this->producer = $producer;
        $this->indexer = $indexer;
    }

    public function reindex(): void
    {
        $this->producer->sendCommand(Commands::REINDEX, null);
    }

    public function reindexIds(array $ids): void
    {
        $this->producer->sendCommand(Commands::REINDEX_IDS, new IdsMessage($ids));
    }

    public function update(array $data, array $criteria): void
    {
        // TODO: Implement update() method.
    }

    public function delete(array $ids): void
    {
        $this->producer->sendCommand(Commands::DELETE, new IdsMessage($ids));
    }

    public function clear(): void
    {
        $this->producer->sendCommand(Commands::CLEAR, null);
    }

    public function getInnerIndexer(): ?Indexer
    {
        return $this->indexer;
    }
}
