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

class ProductSort extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var \Eccube\Entity\Product
     */
    protected $Product;

    /**
     * @var string
     */
    protected $sort01;

    /**
     * Get Product
     *
     * @param \Eccube\Entity\Product $Product
     * @return ProductSort
     */
    public function setProduct(\Eccube\Entity\Product $Product)
    {
        $this->Product = $Product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set sort01
     *
     * @param string $sort01
     * @return ProductSort
     */
    public function setSort01($sort01)
    {
        $this->sort01 = $sort01;
        return $this;
    }

    /**
     * Get sort01
     *
     * @return string
     */
    public function getSort01()
    {
        return $this->sort01;
    }
}