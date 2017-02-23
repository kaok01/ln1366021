<?php

namespace Plugin\CustomEntryForm;

use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\Filesystem\Filesystem;
use Eccube\Util\Cache;
use Eccube\Entity\Master\DeviceType;
use Eccube\Common\Constant;
use Eccube\Entity\PageLayout;
use Eccube\Entity\BlockPosition;
use Eccube\Util\Str;
use Symfony\Component\Finder\Finder;


/**
 * インストールハンドラー
 * Class PluginManager
 * @package Plugin\CustomEntryForm
 */
class PluginManager extends AbstractPluginManager
{

    const BLOCKNAME = "申込みフォームブロック";
    const BLOCKFILENAME = "customentryform_block";
    const BLOCKNAME_PDT = "商品詳細トップブロック";
    const BLOCKFILENAME_PDT = "customproductdetailtop_block";
    private $block;
    private $block_pdt;

    /**
     * PluginManager constructor.
     */
    public function __construct()
    {
        $this->block = sprintf("%s/Resource/template/default/Block/%s.twig", __DIR__, self::BLOCKFILENAME);
        $this->block_pdt = sprintf("%s/Resource/template/default/Block/%s.twig", __DIR__, self::BLOCKFILENAME_PDT);
    }

    /**
     * インストール時に実行
     * @param $config
     * @param $app
     */
    public function install($config, $app)
    {
    }

    /**
     * アンインストール時に実行
     * @param $config
     * @param $app
     */
    public function uninstall($config, $app)
    {
        $this->removeBlock($app);

        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * プラグイン有効化時に実行
     * @param $config
     * @param $app
     */
    public function enable($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
        $this->copyBlock($app);
        $this->addPages($app);


    }

    /**
     * プラグイン無効化時実行
     * @param $config
     * @param $app
     */
    public function disable($config, $app)
    {
        //$this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
        $this->removeBlock($app);

    }

    /**
     * アップデート時に行う処理
     * @param $config
     * @param $app
     */
    public function update($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }

    /**
     * ブロックファイルをブロックディレクトリにコピーしてDBに登録
     *
     * @param $app
     * @throws \Exception
     */
    private function copyBlock($app)
    {
        $this->app = $app;
        $file = new Filesystem();
        $file->copy($this->block, sprintf("%s/%s.twig", $app['config']['block_realdir'], self::BLOCKFILENAME));
        $this->app['orm.em']->getConnection()->beginTransaction();
        try {
            // ブロックの登録
            $Block = $this->registerBlock();
            // BlockPositionの登録
            $this->registerBlockPosition($Block);
            $this->app['orm.em']->getConnection()->commit();
        } catch (\Exception $e) {
            $this->app['orm.em']->getConnection()->rollback();
            throw $e;
        }

        $file->copy($this->block_pdt, sprintf("%s/%s.twig", $app['config']['block_realdir'], self::BLOCKFILENAME_PDT));
        $this->app['orm.em']->getConnection()->beginTransaction();
        try {
            // ブロックの登録
            $Block = $this->registerBlock_PDT();
            // BlockPositionの登録
            $this->registerBlockPosition($Block);
            $this->app['orm.em']->getConnection()->commit();
        } catch (\Exception $e) {
            $this->app['orm.em']->getConnection()->rollback();
            throw $e;
        }

    }

    /**
     * ブロックを削除
     *
     * @param $app
     * @throws \Exception
     */
    private function removeBlock($app)
    {
        // ブロックファイルを削除
        $file = new Filesystem();
        $file->remove(sprintf("%s/%s.twig", $app['config']['block_realdir'], self::BLOCKFILENAME));
        // Blockの取得(file_nameはアプリケーションの仕組み上必ずユニーク)
        /** @var \Eccube\Entity\Block $Block */
        $Block = $app['eccube.repository.block']->findOneBy(array('file_name' => self::BLOCKFILENAME));
        if ($Block)
        {
            $em = $app['orm.em'];
            $em->getConnection()->beginTransaction();
            try {
                // BlockPositionの削除
                $blockPositions = $Block->getBlockPositions();
                /** @var \Eccube\Entity\BlockPosition $BlockPosition */
                foreach ($blockPositions as $BlockPosition)
                {
                    $Block->removeBlockPosition($BlockPosition);
                    $em->remove($BlockPosition);
                }
                // Blockの削除
                $em->remove($Block);
                $em->flush();
                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }
        }
        
        $this->removeBlock_PDT($app);

        Cache::clear($app, false);
    }
    private function removeBlock_PDT($app)
    {
        // ブロックファイルを削除
        $file = new Filesystem();
        $file->remove(sprintf("%s/%s.twig", $app['config']['block_realdir'], self::BLOCKFILENAME_PDT));
        // Blockの取得(file_nameはアプリケーションの仕組み上必ずユニーク)
        /** @var \Eccube\Entity\Block $Block */
        $Block = $app['eccube.repository.block']->findOneBy(array('file_name' => self::BLOCKFILENAME_PDT));
        if ($Block)
        {
            $em = $app['orm.em'];
            $em->getConnection()->beginTransaction();
            try {
                // BlockPositionの削除
                $blockPositions = $Block->getBlockPositions();
                /** @var \Eccube\Entity\BlockPosition $BlockPosition */
                foreach ($blockPositions as $BlockPosition)
                {
                    $Block->removeBlockPosition($BlockPosition);
                    $em->remove($BlockPosition);
                }
                // Blockの削除
                $em->remove($Block);
                $em->flush();
                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }
        }
    }

    /**
     * ブロックの登録
     *
     * @return \Eccube\Entity\Block
     */
    private function registerBlock()
    {
        $DeviceType = $this->app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
        /** @var \Eccube\Entity\Block $Block */
        $Block = $this->app['eccube.repository.block']->findOrCreate(null, $DeviceType);
        $Block->setName(self::BLOCKNAME);
        $Block->setFileName(self::BLOCKFILENAME);
        $Block->setDeletableFlg(Constant::DISABLED);
        $Block->setLogicFlg(1);
        $this->app['orm.em']->persist($Block);
        $this->app['orm.em']->flush($Block);
        return $Block;
    }
    private function registerBlock_PDT()
    {
        $DeviceType = $this->app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
        /** @var \Eccube\Entity\Block $Block */
        $Block = $this->app['eccube.repository.block']->findOrCreate(null, $DeviceType);
        $Block->setName(self::BLOCKNAME_PDT);
        $Block->setFileName(self::BLOCKFILENAME_PDT);
        $Block->setDeletableFlg(Constant::DISABLED);
        $Block->setLogicFlg(1);
        $this->app['orm.em']->persist($Block);
        $this->app['orm.em']->flush($Block);
        return $Block;
    }

    /**
     * BlockPositionの登録
     *
     * @param $Block
     */
    private function registerBlockPosition($Block)
    {
        $blockPos = $this->app['orm.em']->getRepository('Eccube\Entity\BlockPosition')->findOneBy(
            array('page_id' => 1, 'target_id' => PageLayout::TARGET_ID_UNUSED),
            array('block_row' => 'DESC'));

        $BlockPosition = new BlockPosition();

        // ブロックの順序を変更
        if ($blockPos) {
            $blockRow = $blockPos->getBlockRow() + 1;
            $BlockPosition->setBlockRow($blockRow);
        } else {
            // 1番目にセット
            $BlockPosition->setBlockRow(1);
        }

        $PageLayout = $this->app['eccube.repository.page_layout']->find(1);
        $BlockPosition->setPageLayout($PageLayout);
        $BlockPosition->setPageId($PageLayout->getId());
        $BlockPosition->setTargetId(PageLayout::TARGET_ID_UNUSED);
        $BlockPosition->setBlock($Block);
        $BlockPosition->setBlockId($Block->getId());
        $BlockPosition->setAnywhere(Constant::DISABLED);
        $this->app['orm.em']->persist($BlockPosition);
        $this->app['orm.em']->flush($BlockPosition);
    }
    private function addPages($app){
        $cd = 'customentryform';

        $this->createPage($app,'plugin_{$cd}_formentry','form-entry/','商品申し込みフォーム');
        /*
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 14, 'オプションについて', 'options_index', 'Options/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);");
        */

    }

    public function createPage($app,$url,$filename,$pagename,$id = null)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);
        try {

            $PageLayout = $app['eccube.repository.page_layout']
                ->getByUrl($DeviceType,$url);
            if($PageLayout){
                return;
            }
        } catch (\Exception $e) {
            //
        }

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate($id, $DeviceType);

        $editable = true;

        $PageLayout
            ->setUrl($url)
            ->setFileName($filename)
            ->setName($pagename);

        // DB登録
        $app['orm.em']->persist($PageLayout);
        $app['orm.em']->flush();

        // ファイル生成・更新
        $templatePath = $app['eccube.repository.page_layout']->getWriteTemplatePath($editable);
        $filePath = $templatePath.'/'.$PageLayout->getFileName().'.twig';

        $fs = new Filesystem();
        $pageData = "dummycontent";
        $pageData = Str::convertLineFeed($pageData);
        $fs->dumpFile($filePath, $pageData);

        // 更新でファイル名を変更した場合、以前のファイルを削除
        //if ($PageLayout->getFileName() != $fileName && !is_null($fileName)) {
        //    $oldFilePath = $templatePath.'/'.$fileName.'.twig';
        //    if ($fs->exists($oldFilePath)) {
        //        $fs->remove($oldFilePath);
        //    }
        //}


        // twig キャッシュの削除.
        $finder = Finder::create()->in($app['config']['root_dir'].'/app/cache/twig');
        $fs->remove($finder);


    }

}
