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
namespace Plugin\DSContents;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;

/**
 * プラグインイベント処理ルーティングクラス
 * Class DSContents
 * @package Plugin\DSContents
 */
class DSContentsEvent
{
    /**
     * @var string 非会員用セッションキー
     */
    private $sessionKey = 'eccube.front.shopping.nonmember';

    /**
     * @var string 非会員用セッションキー
     */
    private $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';

    /**
     * @var string 受注IDキー
     */
    private $sessionOrderKey = 'eccube.front.shopping.order.id';


    /** @var  \Eccube\Application $app */
    protected $app;

    /**
     * Event constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * フロント画面権限確認
     *
     * @return bool
     */
    protected function isAuthRouteFront()
    {
        return $this->app->isGranted('ROLE_USER');
    }
    public function onAppBefore(){
        $this->rewriteconfig();
    }

    private function rewriteconfig(){

        // load config
        if($this->app->isFrontRequest()){
            $isSP = $this->app->isSmartPhone();
            if($isSP){

                $config = $this->app['config'];
                $config['template_code'] = $this->app['config']['DSContents']['const']['setting']['template_code'];
                $config['block_realdir'] = $this->app['config']['DSContents']['const']['setting']['block_realdir'];
                $config['template_realdir'] = $this->app['config']['DSContents']['const']['setting']['template_realdir'];
                $config['template_html_realdir'] = $this->app['config']['DSContents']['const']['setting']['template_html_realdir'];
                $config['front_urlpath'] = $this->app['config']['DSContents']['const']['setting']['front_urlpath'];
                $config['user_data_realdir'] = $this->app['config']['DSContents']['const']['setting']['user_data_realdir'];
                $this->app['config'] = $config;
            }
        }


    }
    public function onRenderAdminProductIndex(TemplateEvent $event){

    }

    public function onRenderAdminOrderIndex(TemplateEvent $event){

    }
    public function onRenderAdminOrderEdit(TemplateEvent $event){
    }

    public function onFrontShoppingIndexInitialize(EventArgs $event){


    }

    public function onFrontShoppingConfirmInitialize(EventArgs $event){
        $app=$this->app;
        $req=$event->getRequest();
        $sec = $req->getSession();
        $Order = $event->getArgument('Order');

    }
    public function onFrontShoppingConfirmProcessing(EventArgs $event){



    }

    public function onFrontShoppingConfirmComplete(EventArgs $event){

        $app=$this->app;
        $req=$event->getRequest();
        $sec = $req->getSession();
        $Order = $event->getArgument('Order');
        // $email = $form['email']->getData();


    }
    public function onFrontShoppingCompleteInitialize(EventArgs $event){
        $app = $this->app;


    }    
    public function onFrontShoppingPaymentInitialize(EventArgs $event){


    }

    public function onFrontShoppingPaymentComplete(EventArgs $event){



    }



    public function onFrontShoppingDeliveryInitialize(EventArgs $event){



    }
    public function onFrontShoppingDeliveryComplete(EventArgs $event){

    }
    public function onFrontShoppingShippingChangeInitialize(EventArgs $event){



    }
    public function onFrontShoppingShippingComplete(EventArgs $event){


    }
    public function onFrontShoppingShippingEditChangeInitialize(EventArgs $event){

    }
    public function onFrontShoppingShippingEditInitialize(EventArgs $event){



    }
    public function onFrontShoppingShippingEditComplete(EventArgs $event){

    }



    public function onFrontShoppingNonmemberInitialize(EventArgs $event){


    }
    public function onFrontShoppingNonmemberComplete(EventArgs $event){

        $app=$this->app;



    }
    public function onRenderShoppingNonMember(TemplateEvent $event){



    }



    public function onFrontShoppingShippingMultipleChangeInitialize(EventArgs $event){



    }
    public function onFrontShoppingShippingMultipleInitialize(EventArgs $event){


    }
    public function onFrontShoppingShippingMultipleComplete(EventArgs $event){

    }
    public function onFrontShoppingShippingMultipleEditInitialize(EventArgs $event){


    }
    public function onFrontShoppingShippingMultipleEditComplete(EventArgs $event){

    }

    /*
    メール文面に情報を差し込む処理
    プラグインの処理を上書き
    */
    public function onMailServiceMailOrder(EventArgs $event){

    }


    public function onFrontContactIndexComplete(EventArgs $event){
        $app = $this->app;


    }
    public function onFrontProductDetailInitialize(EventArgs $event){
        $app = $this->app;


    }
    public function onFrontProductDetailComplete(EventArgs $event){
        $app = $this->app;


    }
    public function onRenderProductDetail(TemplateEvent $event){

    }



    public function onFrontCartIndexInitialize(EventArgs $event){
        $app = $this->app;




    }
    public function onFrontCartIndexComplete(EventArgs $event){
        $app = $this->app;



    }

    public function onRenderCart(TemplateEvent $event){

    }


    public function onFrontCartAddInitialize(EventArgs $event){
        $app = $this->app;


    }
    public function onFrontCartAddComplete(EventArgs $event){
        $app = $this->app;


    }
    public function onFrontCartAddException(EventArgs $event){
        $app = $this->app;



    }

    public function onRenderMyPageIndex(TemplateEvent $event){
        $app = $this->app;


    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentPageEditComplete($event)
    {
        $this->app['eccube.plugin.DSContents.event']->onAdminContentPageEditComplete($event);
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentPageDeleteComplete($event)
    {
        $this->app['eccube.plugin.DSContents.event']->onAdminContentPageDeleteComplete($event);
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentBlockEditComplete($event)
    {
        $this->app['eccube.plugin.DSContents.event']->onAdminContentBlockEditComplete($event);
    }

    /**
     * @param EventArgs $event
     */
    public function onAdminContentBlockDeleteComplete($event)
    {
        $this->app['eccube.plugin.DSContents.event']->onAdminContentBlockDeleteComplete($event);
    }
}
