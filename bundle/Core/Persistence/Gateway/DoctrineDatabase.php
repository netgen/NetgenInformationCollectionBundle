<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Gateway;

use Doctrine\DBAL\Connection;

class DoctrineDatabase
{
    /**
     * @var Connection
     */
    protected $connection;

    protected $objectsWithCollectionsQuery = <<<EOD
SELECT DISTINCT ezcontentobject.id AS contentobject_id,
	ezcontentobject.name,
	ezcontentobject_tree.main_node_id,
	ezcontentclass.serialized_name_list,
	ezcontentclass.identifier AS class_identifier
FROM ezcontentobject,
	ezcontentobject_tree,
	ezcontentclass
WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
AND ezcontentobject.contentclass_id = ezcontentclass.id
AND ezcontentclass.version = 0
AND ezcontentobject.id IN
( SELECT DISTINCT ezinfocollection.contentobject_id FROM ezinfocollection )
ORDER BY ezcontentobject.name ASC
EOD;

    protected $objectsWithCollectionCountQuery = <<<EOD
SELECT COUNT(*) as count
FROM ezcontentobject,
	ezcontentobject_tree,
	ezcontentclass
WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
AND ezcontentobject.contentclass_id = ezcontentclass.id
AND ezcontentclass.version = 0
AND ezcontentobject.id IN
( SELECT DISTINCT ezinfocollection.contentobject_id FROM ezinfocollection )
ORDER BY ezcontentobject.name ASC;
EOD;


    protected $contentsWithCollectionsCountQuery = <<<EOD
    SELECT COUNT( DISTINCT ezinfocollection.contentobject_id ) as count
FROM ezinfocollection,
	ezcontentobject,
	ezcontentobject_tree
WHERE ezinfocollection.contentobject_id = ezcontentobject.id
AND ezinfocollection.contentobject_id = ezcontentobject_tree.contentobject_id
EOD;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getContentsWithCollectionsCount()
    {
        $query = $this->connection->prepare($this->contentsWithCollectionsCountQuery);

        $query->execute();

        return $query->fetchColumn(0);
    }

    public function getObjectsWithCollections()
    {
        $query = $this->connection->prepare($this->objectsWithCollectionsQuery);

        $query->execute();

        return $query->fetchAll();
    }

    public function getObjectsWithCollectionCount()
    {
        $query = $this->connection->prepare($this->objectsWithCollectionCountQuery);

        $query->execute();

        return $query->fetchColumn(0);
    }
}
