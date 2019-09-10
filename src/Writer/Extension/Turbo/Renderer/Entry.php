<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer\Extension\Turbo\Renderer;

use DOMDocument;
use DOMElement;
use Mf\FeedYaTurbo\Writer\Extension;

/**
*/
class Entry extends Extension\AbstractRenderer
{
    protected $called = false;

    /**
     * Render entry
     *
     * @return void
     */
    public function render()
    {
        $this->_setContent($this->dom, $this->base);
        $this->_setSource($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }

    }

    /**
     * Set entry content
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setContent(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $content = $this->getDataContainer()->getContent();
        if (! $content) {
            return;
        }
        $element = $dom->createElement('turbo:content');
        $root->appendChild($element);
        $cdata = $dom->createCDATASection($content);
        $element->appendChild($cdata);
        $this->called = true;
    }

    /**
     * @return void
     */
    protected function _setSource(DOMDocument $dom, DOMElement $root)
    {
        $source = $this->getDataContainer()->getSource();
        if (empty($source)) {
            return;
        }
        $element = $dom->createElement('turbo:source',$source);
        $root->appendChild($element);
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
