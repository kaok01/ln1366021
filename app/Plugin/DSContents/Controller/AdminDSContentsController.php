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
namespace Plugin\DSContents\Controller;

use Eccube\Application;
use Plugin\DSContents\Form\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 */
class AdminDSContentsController
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $app['monolog.DSContents.admin']->addInfo('index start');

        $form = $app['form.factory']
            ->createBuilder('admin_dscontents_info', null)
            ->getForm();
        $form->get('template_code')->setData($app['config']['DSContents']
            ['const']['setting']['template_code']);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $DSContentsInfo = $form->getData();

            $app->addSuccess('admin.dscontents.save.complete', 'admin');

            $app['monolog.DSContents.admin']->addInfo(
                'index save',
                array(
                    'saveData' => $app['serializer']->serialize($DSContentsInfo, 'json'),
                )
            );

            $app['monolog.DSContents.admin']->addInfo('index end');

            return $app->redirect($app->url('dscontents_info'));
        }

        $app['monolog.DSContents.admin']->addInfo('index end');

        return $app->render(
            'DSContents/Resource/template/admin/dscontentsinfo.twig',
            array(
                'form' => $form->createView(),
                'DSContents' => $DSContentsInfo,
            )
        );
    }
}
