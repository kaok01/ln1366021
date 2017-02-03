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

class FrontCartIndex extends AbstractWorkPlace
{    
    public function createTwig(TemplateEvent $event)
    {   
return;
        $app = $this->app;

        $source = $event->getSource();


        if(preg_match('/<(.*)\s*cart_buystep.*>\n/',$source, $result)){


            $search = $result[0];
dump($result);

            $snipet = file_get_contents($app['config']['plugin_realdir']. '/CustomNonMember/Resource/template/default/Event/Cart/index.twig');
            $replace = $snipet;
            $source = str_replace($search, $replace, $source);
        }
        

        $event->setSource($source);

    }

}
