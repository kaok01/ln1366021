<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Form\Extension\Admin;

use Eccube\Application;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductTypeExtension extends AbstractTypeExtension
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder->add('ProductSort', 'plg_product_sort_column_admin_product_sort', array(
            'mapped' => false,
        ));

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($app) {
            $form = $event->getForm();
            $Product = $form->getData();
            if (strlen($Product->getId())) {
                $ProductSort = $app['eccube.plugin.product_sort_column.repository.product_sort']->find($Product);
                $form['ProductSort']->setData($ProductSort);
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return 'admin_product';
    }
}
