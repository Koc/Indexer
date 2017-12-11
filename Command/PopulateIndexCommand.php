<?php

namespace Brouzie\Components\Indexer\Command;

use Brouzie\Components\Indexer\Async\QueuedIndexer;
use Brouzie\Components\Indexer\IndexationObserver\ConsoleIndexationObserver;
use Brouzie\Components\Indexer\Indexer;
use Brouzie\Components\Indexer\ObservableIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateIndexCommand extends Command
{
    private $indexer;

    public function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('brouzie:indexer:populate-index')
            //TODO: support index argument
//            ->addArgument('index', null, InputArgument::REQUIRED | InputArgument::IS_ARRAY)
            ->addOption('truncate', null, InputOption::VALUE_NONE)
            //TODO: support batch size?
            ->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, '', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $realIndexer = $this->indexer;
        if ($this->indexer instanceof QueuedIndexer && $this->indexer->getInnerIndexer()) {
            $realIndexer = $this->indexer->getInnerIndexer();
        }

        $observer = new ConsoleIndexationObserver($input, $output);

        if ($realIndexer instanceof ObservableIndexer) {
            $realIndexer->setIndexationObserver($observer);
        }

        if ($input->getOption('truncate')) {
            $realIndexer->clear();
        }

        $realIndexer->reindex();
    }
}
