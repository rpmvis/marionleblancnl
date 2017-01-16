<?php

// app/Models/Schema/AppSchema.php
namespace Studio\Models\DBAL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;

/**
 *
 * Schema: object representation of a database schema.
 */
class AppSchema extends Schema {

    /**
     * Overrides parent constructor
     *
     * @param \Doctrine\DBAL\Schema\Table[]      $tables       The schema tables
     * @param \Doctrine\DBAL\Schema\Sequence[]   $sequences    The schema sequences
     * @param \Doctrine\DBAL\Schema\SchemaConfig $schemaConfig The schema config
     * @param array                              $namespaces   Namespaces for tables
     */
    public function __construct(
        array $tables = [], array $sequences = [],
        SchemaConfig $schemaConfig = null, array $namespaces = []) {

        parent::__construct($tables, $sequences, $schemaConfig, $namespaces);

        $this->_addTable($gallTable = new GalleryTable());
        $this->_addTable($workTable = new WorkTable());

//        $this->getTable($postTable->getName())->addColumn(
//                'user_id', 'integer', array('unsigned' => true, 'notnull' => true,)
//        );
//        $this->getTable($postTable->getName())->addForeignKeyConstraint(
//                'user', array('user_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE')
//        );
    }

}
