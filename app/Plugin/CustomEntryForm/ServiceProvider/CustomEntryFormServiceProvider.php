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
namespace Plugin\CustomEntryForm\ServiceProvider;

use Eccube\Application;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class CustomEntryFormServiceProvider
 * @package Plugin\CustomEntryForm\ServiceProvider
 */
class CustomEntryFormServiceProvider implements ServiceProviderInterface
{
    /**
     * サービス登録処理
     * @param BaseApplication $app
     */
    public function register(BaseApplication $app)
    {
        $cd = 'customentryform';

        /**
         * ルーティング登録
         * 管理画面 > 設定 > 基本情報設定 > ＤＳコンテンツ商品基本情報設定画面
         */
        $app->match(
            '/'.$app['config']['admin_route'].'/CustomEntryForm/setting',
            'Plugin\CustomEntryForm\Controller\AdminCustomEntryFormController::index'
        )->bind('plugin_{$cd}_info');


        /**
         * ルーティング登録
         * 管理画面 > 商品一覧 > メニュー > ＤＳコンテンツ商品管理
         */


        /**
         * ルーティング登録
         * 管理画面 > 受注一覧 >　メニュー > ＤＳコンテンツ商品リンク管理
         */


        /**
         * ルーティング登録
         * Mypage >　注文履歴 >  ＤＳコンテンツ商品リンク
         */
        $app->match("/form-entry/{id}/", 'Plugin\CustomEntryForm\Controller\Front\CustomEntryFormController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind("plugin_{$cd}_formentry")
            ;
        $app->post("/form-entry/{id}/bystep", 'Plugin\CustomEntryForm\Controller\Front\CustomEntryFormController::bystep')
            ->bind("plugin_{$cd}_bystep")
            ;

        $app->match("/form-easy/", 'Plugin\CustomEntryForm\Controller\Front\CustomEasyFormController::index')
            ->bind("plugin_{$cd}_formeasy")
            ;
        $app->post("/form-easy/confirm", 'Plugin\CustomEntryForm\Controller\Front\CustomEasyFormController::index')
            ->bind("plugin_{$cd}_formeasy_confirm")
            ;
        $app->match("/form-easy/finish", 'Plugin\CustomEntryForm\Controller\Front\CustomEasyFormController::index')
            ->bind("plugin_{$cd}_formeasy_complete")
            ;

        $app->match("/form-inquiry/", 'Plugin\CustomEntryForm\Controller\Front\CustomContactController::index')
            ->bind("plugin_{$cd}_forminquiry")
            ;
        $app->match("/form-inquiry/confirm", 'Plugin\CustomEntryForm\Controller\Front\CustomContactController::index')
            ->bind("plugin_{$cd}_forminquiry_confirm")
            ;
        $app->match("/form-inquiry/finish", 'Plugin\CustomEntryForm\Controller\Front\CustomContactController::index')
            ->bind("plugin_{$cd}_forminquiry_complete")
            ;

        //詳細ページのコントローラを差替
 
        $app->match('/products/detail/{id}', 'Plugin\CustomEntryForm\Controller\Front\CustomEntryFormController::detail_custom')
        ->bind('product_detail')->assert('id', '\d+');

        /**
         * レポジトリ登録
         */
        $app['eccube.plugin.CustomEntryForm.repository.CustomEntryForm'] = $app->share(
            function () use ($app) {
                return $app['orm.em']->getRepository('Plugin\CustomEntryForm\Entity\CustomEntryForm');
            }
        );


        // ブロックのルーティング設定。ルーティング名の接頭辞にblock_を付ける
        $app->match('/block/customentryform_block', '\Plugin\CustomEntryForm\Controller\Front\Block\CustomEntryFormBlockController::detail_custom')
            ->bind('block_customentryform_block');

        $app->match('/block/customproductdetailtop_block', '\Plugin\CustomEntryForm\Controller\Front\Block\CustomProductDetailTopBlockController::detail_custom')
            ->bind('block_customproductdetailtop_block');



        // サービスの登録
        $app['eccube.plugin.CustomEntryForm.service.CustomEntryForm'] = $app->share(function () use ($app) {
            return new \Plugin\CustomEntryForm\Service\CustomEntryFormService($app);
        });

        /**
         * フォームタイプ登録
         */
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {

            if (isset($app['security']) && isset($app['eccube.repository.customer_favorite_product'])) {
                $types[] = new \Plugin\CustomEntryForm\Form\Type\AddCartType($app['config'], $app['security'], $app['eccube.repository.customer_favorite_product'],$app);
            }
            $types[] = new \Plugin\CustomEntryForm\Form\Type\NonMemberType($app['config']);
            $types[] = new \Plugin\CustomEntryForm\Form\Type\ContactType($app['config']);

            return $types;
        })
        );

        /**
         * メニュー登録
         */
        $app['config'] = $app->share(
            $app->extend(
                'config',
                function ($config) {
                    $addNavi['id'] = "CustomEntryForm_info";
                    $addNavi['name'] = "CustomEntryForm設定";
                    $addNavi['url'] = "CustomEntryForm_info";
                    $nav = $config['nav'];
                    foreach ($nav as $key => $val) {
                        if ("setting" == $val["id"]) {
                            $nav[$key]['child'][0]['child'][] = $addNavi;
                        }
                    }
                    $config['nav'] = $nav;

                    return $config;
                }
            )
        );


        /**
         * メッセージ登録
         */
        $app['translator'] = $app->share(
            $app->extend(
                'translator',
                function ($translator, \Silex\Application $app) {
                    $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
                    $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
                    if (file_exists($file)) {
                        $translator->addResource('yaml', $file, $app['locale']);
                    }

                    return $translator;
                }
            )
        );

        // ログファイル設定
        $app['monolog.CustomEntryForm'] = $this->initLogger($app, 'CustomEntryForm');

        // ログファイル管理画面用設定
        $app['monolog.CustomEntryForm.admin'] = $this->initLogger($app, 'CustomEntryForm_admin');


        // フロント or 管理画面ごとにtwigの探索パスを切り替える.
        // $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig, \Silex\Application $app) {
        //     $paths = array();

        //     // 互換性がないのでprofiler とproduction 時のcacheを分離する
        //     if (isset($app['profiler'])) {
        //         $cacheBaseDir = __DIR__.'/../../app/cache/twig/profiler/';
        //     } else {
        //         $cacheBaseDir = __DIR__.'/../../app/cache/twig/production/';
        //     }

        //     if ($app->isAdminRequest()) {
        //         if (file_exists(__DIR__.'/../../app/template/admin')) {
        //             $paths[] = __DIR__.'/../../app/template/admin';
        //         }
        //         $paths[] = $app['config']['template_admin_realdir'];
        //         $paths[] = __DIR__.'/../../app/Plugin';
        //         $cache = $cacheBaseDir.'admin';

        //     } else {
        //         if (file_exists($app['config']['template_realdir'])) {
        //             $paths[] = $app['config']['template_realdir'];
        //         }
        //         $paths[] = $app['config']['template_default_realdir'];
        //         $paths[] = __DIR__.'/../../app/Plugin';
        //         $cache = $cacheBaseDir.$app['config']['template_code'];
        //         $app['front'] = true;
        //     }
        //     $twig->setCache($cache);
        //     $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

        //     return $twig;
        // }));

    }

    /**
     * 初期化時処理
     *  - 本クラスでは使用せず
     * @param BaseApplication $app
     */
    public function boot(BaseApplication $app)
    {
    }

    /**
     * ＤＳコンテンツ商品プラグイン用ログファイルの初期設定
     *
     * @param BaseApplication $app
     * @param $logFileName
     * @return \Closure
     */
    protected function initLogger(BaseApplication $app, $logFileName)
    {

        return $app->share(function ($app) use ($logFileName) {
            $logger = new $app['monolog.logger.class']('plugin.CustomEntryForm');
            $file = $app['config']['root_dir'].'/app/log/'.$logFileName.'.log';
            $RotateHandler = new RotatingFileHandler($file, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                $logFileName.'_{date}',
                'Y-m-d'
            );

            $token = substr($app['session']->getId(), 0, 8);
            $format = "[%datetime%] [".$token."] %channel%.%level_name%: %message% %context% %extra%\n";
            // $RotateHandler->setFormatter(new LineFormatter($format, null, false, true));
            $RotateHandler->setFormatter(new LineFormatter($format));

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::INFO)
                )
            );

            $logger->pushProcessor(function ($record) {
                // 出力ログからファイル名を削除し、lineを最終項目にセットしなおす
                unset($record['extra']['file']);
                $line = $record['extra']['line'];
                unset($record['extra']['line']);
                $record['extra']['line'] = $line;

                return $record;
            });

            $ip = new IntrospectionProcessor();
            $logger->pushProcessor($ip);

            $web = new WebProcessor();
            $logger->pushProcessor($web);

            // $uid = new UidProcessor(8);
            // $logger->pushProcessor($uid);

            $process = new ProcessIdProcessor();
            $logger->pushProcessor($process);


            return $logger;
        });

    }


}
