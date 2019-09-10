<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mf\FeedYaNews\Writer\Extension\Content\Renderer;

use DOMDocument;
use DOMElement;
use Mf\FeedYaNews\Writer\Extension;

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
        $element = $dom->createElement('yandex:full-text');
        $root->appendChild($element);
        $cdata = $dom->createCDATASection($content);
        $element->appendChild($cdata);
        $this->called = true;
    }

    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute(
            'xmlns:yandex',
            'http://news.yandex.ru'
        );
    }

}
