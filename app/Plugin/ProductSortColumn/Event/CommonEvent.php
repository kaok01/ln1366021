<?php
/*
 * This file is part of the ProductSortColumn
 *
 * Copyright(c) 2017 izayoi256 All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductSortColumn\Event;

use Eccube\Application;

class CommonEvent
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $html
     * @return string
     */
    protected function encodeHtml($html)
    {
        return mb_convert_encoding(str_replace('&amp;', '&', $html), 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     * @param string $view
     * @param array $parameters
     * @return string
     */
    protected function renderView($view, $parameters = array())
    {
        return str_replace('&', '&amp;', $this->app->renderView($view, $parameters));
    }

    /**
     * @param string $html
     * @return array [dom=>\DOMDocument, node=>\DOMNode, template=>\DOMDocumentFragment, xpath=>\DOMXPath]
     */
    protected function initDomParser($html)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding('<!DOCTYPE html>' . $html, 'HTML-ENTITIES', 'auto'));
        $dom->encoding = 'UTF-8';
        $dom->formatOutput = true;
        $template = $dom->createDocumentFragment();
        $node = $dom->importNode($template, true);
        $xpath = new \DOMXPath($dom);
        return compact('dom', 'node', 'template', 'xpath');
    }
}
