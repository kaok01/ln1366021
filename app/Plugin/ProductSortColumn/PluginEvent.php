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

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class PluginEvent
{
    /** @var \Eccube\Application $app */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminProductEditComplete($event)
    {
        $this->app['eccube.plugin.product_sort_column.event.event']->onAdminProductEditComplete($event);
    }

    /**
     * @param TemplateEvent $event
     */
    public function onAdminProductEditRender($event)
    {
        $this->app['eccube.plugin.product_sort_column.event.event']->onAdminProductEditRender($event);
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAdminProductEditRenderBefore($event)
    {
        $this->app['eccube.plugin.product_sort_column.event.event']->onAdminProductEditRenderBefore($event);
    }

    /**
     * @param EventArgs $event
     */
    public function onProductSearch($event)
    {
        $this->app['eccube.plugin.product_sort_column.event.event']->onProductSearch($event);
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentBlockEditInitialize($event)
    {
        $this->app['eccube.plugin.product_sort_column.event.event']->onAdminContentBlockEditInitialize($event);
    }
}
