<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn;

use Doctrine\ORM\QueryBuilder;
use Eccube\Application;
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Master\ProductListOrderBy;
use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{
    /**
     * @param $config
     * @param Application $app
     */
    public function install($config, Application $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code']);
    }

    /**
     * @param $config
     * @param Application $app
     */
    public function uninstall($config, Application $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * @param $config
     * @param Application $app
     */
    public function enable($config, Application $app)
    {
        $this->createOrderBy($config, $app);
        $this->createBlock($config, $app);
    }

    /**
     * @param $config
     * @param Application $app
     */
    public function disable($config, Application $app)
    {
        $this->deleteOrderBy($config, $app);
        $this->deleteBlock($config, $app);
    }

    /**
     * @param $config
     * @param Application $app
     */
    public function update($config, Application $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code']);
    }

    /**
     * @param $config
     * @param Application $app
     */
    protected function createOrderBy($config, Application $app)
    {
        $getNext = function ($col) use ($app) {
            /** @var QueryBuilder $qb */
            $qb = $app['orm.em']->createQueryBuilder();
            return $qb
                    ->select(sprintf('MAX(ob.%s)', $col))
                    ->from('Eccube\Entity\Master\ProductListOrderBy', 'ob')
                    ->getQuery()
                    ->getSingleScalarResult() + 1;
        };

        $Sort01 = new ProductListOrderBy();
        $Sort01
            ->setId($getNext('id'))
            ->setName('ソート項目順')
            ->setRank($getNext('rank'));
        $app['orm.em']->persist($Sort01);
        $app['orm.em']->flush();

        $values = array(
            'sort01_id' => $Sort01->getId(),
        );

        $app['orm.em']->getConnection()->executeUpdate('UPDATE plg_product_sort_column_info SET sort01_id = :sort01_id WHERE id = 1;', $values);
    }

    /**
     * @param $config
     * @param Application $app
     */
    protected function deleteOrderBy($config, Application $app)
    {
        $conn = $app['orm.em']->getConnection();
        $sql = 'SELECT sort01_id FROM plg_product_sort_column_info WHERE id = 1;';
        $rows = $conn->fetchAll($sql);

        $conn->executeUpdate('UPDATE plg_product_sort_column_info SET sort01_id = NULL WHERE id = 1;');

        if (is_array($rows)) {
            foreach ($rows as $cols) {
                foreach ($cols as $col) {
                    $conn->executeUpdate('DELETE FROM mtb_product_list_order_by WHERE id = :id', array('id' => $col));
                }
            }
        }
    }

    /**
     * @param $config
     * @param Application $app
     */
    protected function createBlock($config, Application $app)
    {
        $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);

        /** @var Block $Block */
        $Block = $app['eccube.repository.block']->findOrCreate(null, $DeviceType);
        $Block
            ->setName('ソート商品一覧')
            ->setFileName('plugin_product_sort_column_product_list')
            ->setDeletableFlg(0)
            ->setLogicFlg(1);

        $app['orm.em']->persist($Block);
        $app['orm.em']->flush();
    }

    /**
     * @param $config
     * @param Application $app
     */
    protected function deleteBlock($config, Application $app)
    {
        /** @var QueryBuilder $qb */
        $qb = $app['eccube.repository.block']->createQueryBuilder('b');
        $fileNameLike = 'plugin_product_sort_column_%';
        $qb
            ->delete()
            ->where($qb->expr()->like('b.file_name', ':fileName'))
            ->setParameter('fileName', $fileNameLike)
            ->getQuery()
            ->execute();
    }
}
