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

class Version20170214113800 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('plg_product_sort_column_product_sort');
        $table->addColumn('product_id', 'integer');
        $table->addColumn('sort01', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('product_id'));
        $table->addForeignKeyConstraint('dtb_product', array('product_id'), array('product_id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('plg_product_sort_column_product_sort');
    }
}
