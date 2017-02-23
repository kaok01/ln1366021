<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Plugin\CustomEntryForm\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ゲスト購入のお客様情報入力画面
 */
class NonMemberType extends AbstractType
{
    public $config;

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->config;

        $builder
            ->add('name', 'name', array(
                'required' => true,
            ))
            ->add('kana', 'kana', array(
                'required' => true,
            ))
            ->add('sex', 'sex', array(
                'required' => false,
            ))            
            ->add('company_name', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                ),
            ))
            ->add('zip', 'zip', array(
                'required' => true,
            ))
            ->add('address', 'address', array(
                'required' => true,
            ))
            ->add('tel', 'tel', array(
                'required' => true,
            ))
            ->add('email', 'email', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'customentryform_nonmember';
    }
}
