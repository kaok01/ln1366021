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

class Version20170214133530 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createIndex($schema, 'plg_product_sort_column_product_sort', array('sort01'), 'plg_product_sort_column_product_sort_sort01_idx', array('sort01' => 256));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->dropIndex($schema, 'plg_product_sort_column_product_sort', 'plg_product_sort_column_product_sort_sort01_idx');
    }

    /**
     * @param Schema $schema
     * @param string $tableName
     * @param array $columns
     * @param string $indexName
     * @param array $length
     * @return bool
     */
    protected function createIndex(Schema $schema, $tableName, array $columns, $indexName, array $length = array())
    {
        if (!$schema->hasTable($tableName)) {
            return false;
        }

        $table = $schema->getTable($tableName);
        if (!$table->hasIndex($indexName)) {
            if ($this->connection->getDatabasePlatform()->getName() == 'mysql' && !empty($length)) {
                $cols = array();
                foreach ($length as $column => $len) {
                    $cols[] = sprintf('%s(%d)', $column, $len);
                }
                $this->addSql(sprintf('CREATE INDEX %s ON %s(%s);', $indexName, $tableName, implode(', ', $cols)));
            } else {
                $table->addIndex($columns, $indexName);
            }
            return true;
        }
        return false;
    }

    /**
     * @param Schema $schema
     * @param string $tableName
     * @param string $indexName
     * @return bool
     */
    protected function dropIndex(Schema $schema, $tableName, $indexName)
    {
        if (!$schema->hasTable($tableName)) {
            return false;
        }
        $table = $schema->getTable($tableName);
        if ($table->hasIndex($indexName)) {
            $table->dropIndex($indexName);
            return true;
        }
        return false;
    }
}
