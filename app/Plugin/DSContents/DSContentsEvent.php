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
use Eccube\Exception\ShoppingException;
use Eccube\Exception\CartException;
use Eccube\Entity\CustomerAddress;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Eccube\Common\Constant;

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
    public function onRenderAdminProductIndex(TemplateEvent $event){

        $helper = new Event\WorkPlace\AdminProductIndex();
        $helper->createTwig($event);
    }

    public function onRenderAdminOrderIndex(TemplateEvent $event){

        $helper = new Event\WorkPlace\AdminOrderIndex();
        $helper->createTwig($event);
    }
    public function onRenderAdminOrderEdit(TemplateEvent $event){
        //return;
        $helper = new Event\WorkPlace\AdminOrderEdit();
        $helper->createTwig($event);
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


}
