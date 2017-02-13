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

namespace Plugin\DSContents\Event;

use Eccube\Application;
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Event\EventArgs;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;

class Event
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentPageEditComplete($event)
    {
        $app = $this->app;
        $templatePath = $event->getArgument('templatePath');
        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $event->getArgument('form');

        /** @var PageLayout $PageLayout */
        $PageLayout = $event->getArgument('PageLayout');

        if ($PageLayout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {

            $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_SP);
            $conditions = array(
                'url' => $PageLayout->getUrl(),
                'DeviceType' => $DeviceType,
            );
            $SpPageLayout = $app['eccube.repository.page_layout']->findOneBy($conditions);

            if (!$SpPageLayout) {
                $relpath = $PageLayout->getEditFlg() == PageLayout::EDIT_FLG_DEFAULT ?
                    $app['config']['DSContents']['const']['template_sphone_relpath']:
                    $app['config']['DSContents']['const']['user_data_sphone_relpath'];
                $relpath = strlen($relpath) ?
                    $relpath . '/' :
                    $relpath;
                $SpPageLayout = clone $PageLayout;
                $SpPageLayout
                    ->setFileName($relpath . $SpPageLayout->getFileName())
                    ->setDeviceType($DeviceType);
                $app['orm.em']->persist($SpPageLayout);
                $app['orm.em']->flush();
                $app->addSuccess('admin.plugin.dscontents.page.copy.db', 'admin');
            }

            $fs = new Filesystem();
            $filePath = $templatePath . '/' . $SpPageLayout->getFileName() . '.twig';
            if (!$fs->exists($filePath)) {
                $pageData = Str::convertLineFeed($form->get('tpl_data')->getData());
                $fs->dumpFile($filePath, $pageData);
                $app->addSuccess('admin.plugin.dscontents.page.copy.file', 'admin');
            }
        }
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentPageDeleteComplete($event)
    {
        $app = $this->app;
        $templatePath = $app['eccube.repository.page_layout']->getWriteTemplatePath(true);
        /** @var PageLayout $PageLayout */
        $PageLayout = $event->getArgument('PageLayout');

        if ($PageLayout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {

            $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_SP);
            $conditions = array(
                'url' => $PageLayout->getUrl(),
                'DeviceType' => $DeviceType,
            );
            /** @var PageLayout $SpPageLayout */
            $SpPageLayout = $app['eccube.repository.page_layout']->findOneBy($conditions);

            if ($SpPageLayout) {

                $filePath = $templatePath . '/' . $SpPageLayout->getFileName() . '.twig';

                $app['orm.em']->remove($SpPageLayout);
                $app['orm.em']->flush();
                $app->addSuccess('admin.plugin.dscontents.page.delete.db', 'admin');

                $fs = new Filesystem();
                if ($fs->exists($filePath)) {
                    $fs->remove($filePath);
                    $app->addSuccess('admin.plugin.dscontents.page.delete.file', 'admin');
                }
            }
        }
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentBlockEditComplete($event)
    {
        $app = $this->app;
        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $event->getArgument('form');

        /** @var Block $Block */
        $Block = $event->getArgument('Block');

        if ($Block->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {

            $relpath = $app['config']['DSContents']['const']['block_sphone_relpath'];
            $relpath = strlen($app['config']['DSContents']['const']['block_sphone_relpath']) ?
                $relpath . '/' :
                $relpath;
            $fileName = $relpath . $Block->getFileName();

            $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_SP);
            $conditions = array(
                'file_name' => $fileName,
                'DeviceType' => $DeviceType,
            );
            $SpBlock = $app['eccube.repository.block']->findOneBy($conditions);

            if (!$SpBlock) {
                $SpBlock = clone $Block;
                $SpBlock
                    ->setFileName($fileName)
                    ->setDeviceType($DeviceType);
                $app['orm.em']->persist($SpBlock);
                $app['orm.em']->flush();
                $app->addSuccess('admin.plugin.dscontents.block.copy.db', 'admin');
            }

            $fs = new Filesystem();
            $filePath = $app['config']['block_realdir'] . '/' . $fileName . '.twig';
            if (!$fs->exists($filePath)) {
                $blockData = Str::convertLineFeed($form->get('block_html')->getData());
                $fs->dumpFile($filePath, $blockData);
                $app->addSuccess('admin.plugin.dscontents.block.copy.file', 'admin');
            }
        }
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentBlockDeleteComplete($event)
    {
        $app = $this->app;
        /** @var Block $Block */
        $Block = $event->getArgument('Block');

        if ($Block->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {

            $relpath = $app['config']['DSContents']['const']['block_sphone_relpath'];
            $relpath = strlen($app['config']['DSContents']['const']['block_sphone_relpath']) ?
                $relpath . '/' :
                $relpath;
            $fileName = $relpath . $Block->getFileName();

            $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_SP);
            $conditions = array(
                'file_name' => $fileName,
                'DeviceType' => $DeviceType,
            );
            $SpBlock = $app['eccube.repository.block']->findOneBy($conditions);

            if ($SpBlock) {

                $app['orm.em']->remove($SpBlock);
                $app['orm.em']->flush();
                $app->addSuccess('admin.plugin.dscontents.block.delete.db', 'admin');

                $fs = new Filesystem();
                $filePath = $app['config']['block_realdir'] . '/' . $fileName . '.twig';
                if ($fs->exists($filePath)) {
                    $fs->remove($filePath);
                    $app->addSuccess('admin.plugin.dscontents.block.delete.file', 'admin');
                }
            }
        }
    }
}
