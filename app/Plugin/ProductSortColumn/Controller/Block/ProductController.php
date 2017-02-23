<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Controller\Block;

use Doctrine\ORM\QueryBuilder;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EventArgs;
use Plugin\ProductSortColumn\Entity\Info;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        /** @var Info $Info */
        $Info = $app['eccube.plugin.product_sort_column.repository.info']->get();
        $repository = $app['eccube.plugin.product_sort_column.repository.master.product_list_order_by'];

        $getProducts = function ($searchData) use ($app) {
            /** @var QueryBuilder $qb */
            $qb = $app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
            $event = new EventArgs(compact('qb', 'searchData'));
            $app['eccube.plugin.product_sort_column.event.event']->onProductSearch($event);
            return $app['paginator']()->paginate($qb, 1, $app['product_sort_column_config']['product_sort_list_limit']);
        };

        $parameters = array(
            'LowerPriceProducts'    => $getProducts(array('orderby' => $repository->find(1))),
            'NewerProducts'         => $getProducts(array('orderby' => $repository->find(2))),
            'Sort01Products'        => $getProducts(array('orderby' => $Info->getSort01())),
        );
        return $app->render('Block/plugin_product_sort_column_product_list.twig', $parameters);
    }
}
