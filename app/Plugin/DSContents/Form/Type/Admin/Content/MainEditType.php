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

namespace Plugin\DSContents\Form\Type\Admin\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MainEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $name = 'file_name';
        $fileName = $builder->get($name);
        $type = $fileName->getType()->getName();
        $options = $fileName->getOptions();
        $options['constraints'] = !empty($options['constraints']) ?
            array_map(function ($constraint) {
                return $constraint instanceof Assert\Regex ?
                    new Assert\Regex(array('pattern' => '/^([0-9a-zA-Z_\-\.]+\/?)+(?<!\/)$/')) :
                    $constraint;
            }, $options['constraints']) :
            array();
        $builder->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'main_edit';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'plugin_dscontents_main_edit';
    }
}
