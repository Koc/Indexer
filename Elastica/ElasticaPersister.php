<?php

namespace Brouzie\Components\Indexer\Elastica;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Persister;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query\MatchAll;

class ElasticaPersister implements Persister
{
    private $index;

    private $options;

    public function __construct(Index $index, array $options = [])
    {
        $this->index = $index;
        $this->options = array_replace(
            [
                'routing_field' => null,
                'type' => null,
            ],
            $options
        );

        if (empty($this->options['type'])) {
            throw new \InvalidArgumentException('Type is required.');
        }
    }

    /**
     * @param Entry[] $entries
     */
    public function persist(array $entries): void
    {
        $documents = [];
        foreach ($entries as $entry) {
            $data = $entry->getDocumentData();
            $document = new Document($entry->getId(), $data, $this->options['type']);

            if (null !== $key = $this->options['routing_field']) {
                $document->setRouting($data[$key]);
            }

            $document->setUpsert($data);
            $documents[] = $document;
        }

        $this->index->addDocuments($documents);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete(array $ids): void
    {
        $documents = [];
        foreach ($ids as $id) {
            $document = new Document($id, [], $this->options['type']);
            $documents[] = $document;
        }

        $this->index->deleteDocuments($documents);
    }

    public function clear(): void
    {
        $this->index->deleteByQuery(new MatchAll());
    }
}
