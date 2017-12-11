<?php

namespace Brouzie\Components\Indexer\Provider;

use Brouzie\Components\Indexer\CountableProvider;
use Brouzie\Components\Indexer\Provider;
use Psr\Log\LoggerInterface;

class TracerableProvider implements CountableProvider
{
    private $provider;

    private $logger;

    public function __construct(Provider $provider, LoggerInterface $logger)
    {
        $this->provider = $provider;
        $this->logger = $logger;
    }

    public function getIdsBatches(int $batchSize): iterable
    {
        $this->logger->debug('Executing "getIdsBatches" method.');
        $time = microtime(true);

        foreach ($this->provider->getIdsBatches($batchSize) as $batchNumber => $idsBatch) {
            $this->logger->debug(sprintf('Method "getIdsBatches" executed in %.4fs.', microtime(true) - $time));

            yield $batchNumber => $idsBatch;
            $time = microtime(true);
        }
    }

    public function getByIds(array $ids): iterable
    {
        $time = microtime(true);
        $this->logger->debug('Executing "getByIds" method.');

        $result = $this->provider->getByIds($ids);

        $this->logger->debug(sprintf('Method "getByIds" executed in %.4fs.', microtime(true) - $time));

        return $result;
    }

    public function getIdsCount(): int
    {
        if (!$this->provider instanceof CountableProvider) {
            throw new \BadMethodCallException('Inner provider does not supports counting.');
        }

        $time = microtime(true);
        $this->logger->debug('Executing "getIdsCount" method.');

        $result = $this->provider->getIdsCount();

        $this->logger->debug(sprintf('Method "getIdsCount" executed in %.4fs.', microtime(true) - $time));

        return $result;
    }
}
