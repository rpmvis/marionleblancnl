<?php

// src/Models/DBAL/GalleryTable.php

namespace Studio\Models\DBAL;

use Doctrine\DBAL\Schema\Table;

class GalleryTable extends Table {

    /**
     * Overrides parent constructor
     * @param string $tableName The table name
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(string $tableName = 'post') {
        parent::__construct($tableName);

        $this->_build();
    }

    /**
     * Build table
     * @return void
     */
    private function _build() {// created
        $this->addColumn(
            'id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto',)
        );
        $this->addColumn(
            'created', 'date', array('notnull' => true)
        );
        $this->addColumn(
            'title', 'string', array('length' => 255, 'notnull' => true)
        );
        $this->addColumn(
            'body', 'text', array('notnull' => true)
        );
        $this->setPrimaryKey(array('id'));
        $this->addUniqueIndex(array('title',));
    }

}
