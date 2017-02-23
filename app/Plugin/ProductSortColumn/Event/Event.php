<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Event;

use Doctrine\ORM\QueryBuilder;
use Eccube\Entity\Block;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\ProductSortColumn\Entity\Info;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Event extends CommonEvent
{
    /** @var FormView */
    protected $adminProductEditFormView;

    /**
     * @param EventArgs $event
     */
    public function onAdminProductEditComplete($event)
    {
        $app = $this->app;
        $form = $event->getArgument('form');
        $Product = $event->getArgument('Product');
        $ProductSort = $form['ProductSort']->getData();
        $ProductSort->setProduct($Product);
        $app['orm.em']->persist($ProductSort);
        $app['orm.em']->flush();
    }

    /**
     * @param TemplateEvent $event
     */
    public function onAdminProductEditRender($event)
    {
        $parameters = $event->getParameters();
        $this->adminProductEditFormView = $parameters['form'];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAdminProductEditRenderBefore($event)
    {
        $form = $this->adminProductEditFormView;
        if (is_null($form)) {
            return;
        }

        $app = $this->app;

        $response = $event->getResponse();
        $html = $response->getContent();

        /** @var \DOMDocument $dom */
        /** @var \DOMDocumentFragment $template */
        /** @var \DOMXPath $xpath */
        /** @var \DOMNode $node */
        extract($this->initDomParser($html));

        $parameters = compact('form');
        $twig = $app->renderView('ProductSortColumn/Resource/template/admin/Product/snippet_product_sort.twig', $parameters);
        $template->appendXML($twig);

        $element = $xpath->query('id("detail_tag_box")')->item(0);
        if ($element) {
            $element->parentNode->insertBefore($node, $element->nextSibling);
        }

        $response->setContent(mb_convert_encoding($dom->saveHTML(), 'UTF-8', 'HTML-ENTITIES'));
        $event->setResponse($response);
    }

    /**
     * @param EventArgs $event
     */
    public function onProductSearch($event)
    {
        $app = $this->app;
        /** @var Info $Info */
        $Info = $app['eccube.plugin.product_sort_column.repository.info']->get();

        /** @var QueryBuilder $qb */
        $qb = $event->getArgument('qb');
        $searchData = $event->getArgument('searchData');
        $join = false;

        if (!empty($searchData['orderby'])) {
            if ($Info->getSort01() && $searchData['orderby']->getId() == $Info->getSort01()->getId()) {
                $join = true;
                $qb
                    ->orderBy('ps.sort01', 'ASC')
                    ->addOrderBy('p.id', 'DESC');
            }
        }

        if ($join) {
            $qb->leftJoin('Plugin\ProductSortColumn\Entity\ProductSort', 'ps', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ps.Product');
        }
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentBlockEditInitialize($event)
    {
        $app = $this->app;

        /** @var Block $Block */
        $Block = $event->getArgument('Block');
        $fileName = $Block->getFileName();
        $html = '';

        $readPaths = array(
            $app['config']['block_realdir'],
            $app['config']['block_default_realdir'],
            sprintf('%s/ProductSortColumn/Resource/template/%s/Block', $app['config']['plugin_realdir'], $app['config']['template_code']),
        );

        foreach ($readPaths as $readPath) {
            $filePath = $readPath . '/' . $fileName . '.twig';
            $fs = new Filesystem();
            if ($fs->exists($filePath)) {
                $html = file_get_contents($filePath);
                break;
            }
        }

        $event->setArgument('html', $html);
    }
}
