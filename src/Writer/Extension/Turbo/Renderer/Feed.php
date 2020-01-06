<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer\Extension\Turbo\Renderer;

use DOMDocument;
use DOMElement;
use DOMAttr;
use Mf\FeedYaTurbo\Writer\Extension;

/**
*/
class Feed extends Extension\AbstractRenderer
{
    protected $called = false;

    /**
     * Render entry
     *
     * @return void
     */
    public function render()
    {
        $this->_setAnalytics($this->dom, $this->base);
        $this->_setNetwork($this->dom, $this->base);
        
        if ($this->called) {
            $this->_appendNamespaces();
        }

    }

    /**
     * @return void
     */
    protected function _setAnalytics(DOMDocument $dom, DOMElement $root)
    {
        $analytics = $this->getDataContainer()->getAnalytics();
        if (empty($analytics) || !is_array($analytics)) {
            return;
        }
        foreach ($analytics as $a){
            $element = $dom->createElement('turbo:analytics');
            $element->setAttributeNode(new DOMAttr('type', $a["type"]));
            $element->setAttributeNode(new DOMAttr('id', $a["id"]));
            if (!empty($a["params"])){
                $element->setAttributeNode(new DOMAttr('params', $a["params"]));
            }
            $root->appendChild($element);
        }

        $this->called = true;
    }
    
    /**
     * @return void
     */
    protected function _setNetwork(DOMDocument $dom, DOMElement $root)
    {
        $network = $this->getDataContainer()->getNetwork();
        if (empty($network) || !is_array($network)) {
            return;
        }
        foreach ($network as $a){
            $element = $dom->createElement('turbo:network');
            $element->setAttributeNode(new DOMAttr('type', $a["type"]));
            $element->setAttributeNode(new DOMAttr('turbo-ad-id', $a["turbo-ad-id"]));
            if (!empty($a["content"])){
                $content = $dom->createCDATASection($a["content"]);
                $element->appendChild($content);
            }
            $root->appendChild($element);
        }

        $this->called = true;
    }

    
    
    
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute(
            'xmlns:turbo',
            'http://turbo.yandex.ru'
        );
    }

}
