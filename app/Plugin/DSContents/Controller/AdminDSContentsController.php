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
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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
            $configfile = $app['config']['plugin_realdir'].'/DSContents/config.yml';

            $yaml = new Parser();

            $value = $yaml->parse(file_get_contents($configfile));


            $value['const']['setting']['template_code']=$DSContentsInfo['template_code'];
            $value['const']['setting']['template_realdir']=realpath($app['config']['template_realdir'].'/../'.$DSContentsInfo['template_code']);
            $value['const']['setting']['block_realdir']=$value['const']['setting']['template_realdir'].'/Block';
            $value['const']['setting']['template_html_realdir']=realpath($app['config']['template_html_realdir'].'/../'.$DSContentsInfo['template_code']);
            $value['const']['setting']['front_urlpath']=$app['config']['front_urlpath'].'/../'.$DSContentsInfo['template_code'];
            $value['const']['setting']['user_data_realdir']=$app['config']['user_data_realdir'].'/../'.$DSContentsInfo['template_code'];


            $dumper = new Dumper();

            $yaml = $dumper->dump($value,3);

            file_put_contents($configfile, $yaml);
            
            //キャッシュ消す
            // twig キャッシュの削除.
            $fs = new Filesystem();

            $finder = Finder::create()->in($app['config']['root_dir'].'/app/cache/plugin');
            $fs->remove($finder);


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
