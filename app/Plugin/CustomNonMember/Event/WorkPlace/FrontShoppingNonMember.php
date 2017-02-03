<?php


namespace Plugin\CustomNonMember\Event\WorkPlace;

use Eccube\Common\Constant;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Validator\Constraints as Assert;

class FrontShoppingNonMember extends AbstractWorkPlace
{    
    public function createTwig(TemplateEvent $event)
    {   
        //return;
        //一括通知と被るので中断

        $app = $this->app;

        $source = $event->getSource();


        if(preg_match('/history_list__detail_button.*>\n/',$source, $result)){
        //if(preg_match('/<(.*)\s*id="admin_order_delete.*>\n/',$source, $result)){


            $search = $result[0];


            $snipet = file_get_contents($app['config']['plugin_realdir']. '/CustomNonMember/Resource/template/default/Event/Shopping/nonmember_index.twig');
            $replace = $search. $snipet;
            $source = str_replace($search, $replace, $source);
        }
        

        $event->setSource($source);

    }

}
