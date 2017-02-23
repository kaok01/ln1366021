<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Util;

use Eccube\Common\Constant;

class Version
{
    /**
     * Check version to support get instance function. (monolog, new style, ...)
     *
     * @return mixed
     */
    public static function isSupportGetInstanceFunction()
    {
        return version_compare(Constant::VERSION, '3.0.9', '>=');
    }

    /**
     * Check version to support new log function.
     *
     * @return mixed
     */
    public static function isSupportLogFunction()
    {
        return version_compare(Constant::VERSION, '3.0.12', '>=');
    }

    /**
     * Check version to support new hookpoint.
     *
     * @return mixed
     */
    public static function isSupportNewHookPoint()
    {
        return version_compare(Constant::VERSION, '3.0.9', '>=');
    }
}