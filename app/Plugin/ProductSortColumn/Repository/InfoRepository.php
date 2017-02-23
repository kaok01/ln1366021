<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Repository;

use Doctrine\ORM\EntityRepository;
use Eccube\Application;
use Plugin\ProductSortColumn\Entity\Info;

class InfoRepository extends EntityRepository
{
    protected $app;

    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param int $id
     * @return null|Info
     */
    public function get($id = 1)
    {
        $lifetime = $this->app['config']['doctrine_cache']['result_cache']['lifetime'];

        $qb = $this->createQueryBuilder('i')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $qb
            ->getQuery()
            ->useResultCache(true, $lifetime)
            ->getOneOrNullResult();
    }
}