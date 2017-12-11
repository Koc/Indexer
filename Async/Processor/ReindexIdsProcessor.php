<?php

namespace Brouzie\Components\Indexer\Async\Processor;

use Brouzie\Components\Indexer\Async\Commands;
use Brouzie\Components\Indexer\Async\Message\IdsMessage;
use Brouzie\Components\Indexer\Indexer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\QueueSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;

class ReindexIdsProcessor implements PsrProcessor, CommandSubscriberInterface, QueueSubscriberInterface
{
    private $indexer;

    public function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;
    }

    public function process(PsrMessage $message, PsrContext $context)
    {
        try {
            $idsMessage = IdsMessage::jsonDeserialize($message->getBody());
        } catch (\Exception $e) {
            return Result::reject($e->getMessage());
        }

        $this->indexer->reindexIds($idsMessage->getIds());

        return self::ACK;
    }

    public static function getSubscribedCommand()
    {
        return [
            'processorName' => Commands::REINDEX_IDS,
            'queueName' => Commands::REINDEX_IDS,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }

    public static function getSubscribedQueues()
    {
        return [Commands::REINDEX_IDS];
    }
}
