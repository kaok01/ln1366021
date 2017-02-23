<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Entity;

use Eccube\Entity\Master\ProductListOrderBy;

class Info extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ProductListOrderBy
     */
    protected $Sort01;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Sort01
     *
     * @param ProductListOrderBy $Sort01
     * @return Info
     */
    public function setSort01($Sort01)
    {
        $this->Sort01 = $Sort01;
        return $this;
    }

    /**
     * Get Sort01
     *
     * @return ProductListOrderBy
     */
    public function getSort01()
    {
        return $this->Sort01;
    }
}