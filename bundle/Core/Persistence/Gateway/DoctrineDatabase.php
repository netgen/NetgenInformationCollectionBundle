<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use PDO;

class DoctrineDatabase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * DoctrineDatabase constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns number of content objects that have any collection
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getContentsWithCollectionsCount()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(
                'COUNT(DISTINCT eic.contentobject_id) AS count'
            )
            ->from($this->connection->quoteIdentifier('ezinfocollection'), 'eic')
            ->innerJoin(
                'eic',
                $this->connection->quoteIdentifier('ezcontentobject'),
                'eco',
                $query->expr()->eq(
                    $this->connection->quoteIdentifier('eic.contentobject_id'),
                    $this->connection->quoteIdentifier('eco.id')
                )
            )
            ->leftJoin(
                'eic',
                $this->connection->quoteIdentifier('ezcontentobject_tree'),
                'ecot',
                $query->expr()->eq(
                    $this->connection->quoteIdentifier('eic.contentobject_id'),
                    $this->connection->quoteIdentifier('ecot.contentobject_id')
                )
            );

        $statement = $query->execute();

        return (int)$statement->fetchColumn();
    }

    /**
     * Returns content objects with their collections
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getObjectsWithCollections($limit, $offset)
    {
        $contentIdsQuery = $this->connection->createQueryBuilder();
        $contentIdsQuery
            ->select("DISTINCT contentobject_id AS id")
            ->from($this->connection->quoteIdentifier('ezinfocollection'));

        $statement = $contentIdsQuery->execute();

        $contents = [];
        foreach ($statement->fetchAll() as $content) {
            $contents[] = (int)$content['id'];
        }

        if (empty($contents)) {
            return [];
        }

        $query = $this->connection->createQueryBuilder();
        $query
            ->select(
                "eco.id AS content_id",
                "ecot.main_node_id"
            )
            ->from($this->connection->quoteIdentifier('ezcontentobject'), 'eco')
            ->leftJoin(
                'eco',
                $this->connection->quoteIdentifier('ezcontentobject_tree'),
                'ecot',
                $query->expr()->eq(
                    $this->connection->quoteIdentifier('eco.id'),
                    $this->connection->quoteIdentifier('ecot.contentobject_id')
                )
            )
            ->innerJoin(
                'eco',
                $this->connection->quoteIdentifier('ezcontentclass'),
                'ecc',
                $query->expr()->eq(
                    $this->connection->quoteIdentifier('eco.contentclass_id'),
                    $this->connection->quoteIdentifier('ecc.id')
                )
            )
            ->where(
                $query->expr()->eq('ecc.version', 0)
            )
            ->andWhere($query->expr()->in('eco.id', $contents))
            ->groupBy([
                $this->connection->quoteIdentifier('ecot.main_node_id'),
                $this->connection->quoteIdentifier('content_id'),
            ])
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $statement = $query->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
