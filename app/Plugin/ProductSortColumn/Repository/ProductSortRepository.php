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
use Plugin\ProductSortColumn\Entity\ProductSort;

class ProductSortRepository extends EntityRepository
{
    protected $app;

    public function setApplication(Application $app)
    {
        $this->app = $app;
    }
}