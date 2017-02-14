<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2016 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Plugin\DSContents\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DataImportInfoType
 * @package Plugin\DSContents\Form\Type
 */
class DSContentsInfoType extends AbstractType
{
    /** @var \Eccube\Application */
    protected $app;
    /** @var array */
    protected $orderStatus;

    /**
     * DataImportInfoType constructor.
     * @param \Eccube\Application $app
     */
    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
        // // 全受注ステータス ID・名称 取得保持
        // $this->orderStatus = array();
        // $this->app['orm.em']->getFilters()->enable('incomplete_order_status_hidden');
        // foreach ($this->app['eccube.repository.order_status']->findAllArray() as $id => $node) {
        //     $this->orderStatus[$id] = $node['name'];
        // }
        // $this->app['orm.em']->getFilters()->disable('incomplete_order_status_hidden');
    }

    /**
     * Build config type form
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plg_add_dataimport_status',
                'text',
                array(
                    'label' => 'SP用テンプレートコード',
                    'required' => true,
                    'empty_data' => null,
                    'mapped' => false,
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                )
            )
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefaults(
        //     array(
        //         'data_class' => 'Plugin\DataImport\Entity\DataImportInfo',
        //     )
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_dscontents_info';
    }
}
