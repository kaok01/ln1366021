<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170214135000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('plg_product_sort_column_info');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('sort01_id', 'smallint', array('notnull' => false));
        $table->setPrimaryKey(array('id'));
        $table->addForeignKeyConstraint('mtb_product_list_order_by', array('sort01_id'), array('id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('plg_product_sort_column_info');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $this->connection->executeUpdate('INSERT INTO plg_product_sort_column_info () VALUES ();');
    }
}
