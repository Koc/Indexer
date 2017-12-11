<?php

namespace Brouzie\Components\Indexer\Doctrine;

use Brouzie\Components\Indexer\CountableProvider;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class DoctrineQueryBuilderProvider implements CountableProvider
{
    private $em;

    private $options;

    public function __construct(EntityManagerInterface $em, string $entity, array $options = []) {
        $this->em = $em;
        $this->options = array_replace(
            [
                'entity' => $entity,
                'query_builder_method_name' => 'createQueryBuilder',
                'query_builder_method_args' => ['e'],
                'entity_alias' => 'e',
                'id_field' => 'id',
                'hydration_mode' => AbstractQuery::HYDRATE_OBJECT,
                'requires_distinct_count' => false,
                'supports_ids_ordering' => true,
            ],
            $options
        );
    }

    public function getIdsBatches(int $batchSize): iterable
    {
        $firstResult = 0;
        $batchNumber = 0;
        $lastId = null;
        do {
            $queryBuilder = $this
                ->getQueryBuilder()
                ->select(sprintf('%s as _id', $this->getIdField()))
                ->orderBy($this->getIdField())
                ->setMaxResults($batchSize);

            if ($this->options['supports_ids_ordering']) {
                if (null !== $lastId) {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->gt($this->getIdField(), ':last_id'))
                        ->setParameter('last_id', $lastId);
                }
            } else {
                $queryBuilder
                    ->setFirstResult($firstResult);
                $firstResult += $batchSize;
            }

            $this->prepareSelectIdsQueryBuilder($queryBuilder);

            $result = $queryBuilder
                ->getQuery()
                ->getResult();

            $idsBatch = array_column($result, '_id');
            if ($this->options['supports_ids_ordering'] && $idsBatch) {
                $lastId = end($idsBatch);
            }

            yield $batchNumber => $idsBatch;
            $batchNumber++;
        } while ($idsBatch);
    }

    public function getByIds(array $ids): iterable
    {
//        $usedMemory = round(memory_get_usage() / 1024 / 1024);
//        dump('getByIds', $usedMemory);
//        if ($usedMemory > 40) {
//            exit;
//        }
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->andWhere($queryBuilder->expr()->in($this->getIdField(), ':ids'))
            ->setParameter('ids', $ids);

        $this->prepareSelectQueryBuilder($queryBuilder);

        $result = $queryBuilder
            ->getQuery()
            ->getResult($this->options['hydration_mode']);

        if ($result) {
            $result = $this->transformResult($result);
        }

        return $result;
    }

    public function getIdsCount(): int
    {
        $queryBuilder = $this->getQueryBuilder();

        $expr = $queryBuilder->expr();
        $countExpr = $expr->count($this->getIdField());
        if ($this->options['requires_distinct_count']) {
            $countExpr = $expr->countDistinct($this->getIdField());
        }

        $queryBuilder
            ->select($countExpr);

        $this->prepareSelectCountQueryBuilder($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    protected function prepareSelectIdsQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // override this method if you need modify query builder
    }

    protected function prepareSelectCountQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // override this method if you need modify query builder
    }

    protected function prepareSelectQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // override this method if you need modify query builder
    }

    protected function transformResult(array $result): array
    {
        // override this method if you need modify results
        return $result;
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $repository = $this->em->getRepository($this->options['entity']);
        $method = $this->options['query_builder_method_name'];

        return $repository->$method(...$this->options['query_builder_method_args']);
    }

    protected function getIdField(): string
    {
        return sprintf('%s.%s', $this->options['entity_alias'], $this->options['id_field']);
    }
}
