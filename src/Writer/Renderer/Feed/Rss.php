<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer\Renderer\Feed;

use DateTime;
use DOMDocument;
use DOMElement;
use Mf\FeedYaTurbo\Uri;
use Mf\FeedYaTurbo\Writer;
use Mf\FeedYaTurbo\Writer\Renderer;
use Mf\FeedYaTurbo\Writer\Version;

/**
*/
class Rss extends Renderer\AbstractRenderer implements Renderer\RendererInterface
{
    /**
     * Constructor
     *
     * @param  Writer\Feed $container
     */
    public function __construct(Writer\Feed $container)
    {
        parent::__construct($container);
    }

    /**
     * Render RSS feed
     *
     * @return self
     */
    public function render()
    {
        $this->dom = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput = true;
        $this->dom->substituteEntities = false;
        $rss = $this->dom->createElement('rss');
        $this->setRootElement($rss);
        $rss->setAttribute('version', '2.0');
        $rss->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');

        $channel = $this->dom->createElement('channel');
        $rss->appendChild($channel);
        $this->dom->appendChild($rss);
        $this->_setLanguage($this->dom, $channel);
        $this->_setTitle($this->dom, $channel);
        $this->_setDescription($this->dom, $channel);
        $this->_setLink($this->dom, $channel);

        foreach ($this->extensions as $ext) {
            $ext->setType($this->getType());
            $ext->setRootElement($this->getRootElement());
            $ext->setDomDocument($this->getDomDocument(), $channel);
            $ext->render();
        }

        foreach ($this->container as $entry) {
            if ($this->getDataContainer()->getEncoding()) {
                $entry->setEncoding($this->getDataContainer()->getEncoding());
            }
            if ($entry instanceof Writer\Entry) {
                $renderer = new Renderer\Entry\Rss($entry);
            } else {
                continue;
            }
            if ($this->ignoreExceptions === true) {
                $renderer->ignoreExceptions();
            }
            $renderer->setType($this->getType());
            $renderer->setRootElement($this->dom->documentElement);
            $renderer->render();
            $element = $renderer->getElement();
            $deep = version_compare(PHP_VERSION, '7', 'ge') ? 1 : true;
            $imported = $this->dom->importNode($element, $deep);
            $channel->appendChild($imported);
        }
        return $this;
    }

    /**
     * Set feed language
     *
     * @param DOMDocument $dom
     * @param DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setLanguage(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $lang = $this->getDataContainer()->getLanguage();
        if (! $lang) {
            return;
        }
        $language = $dom->createElement('language');
        $root->appendChild($language);
        $language->nodeValue = $lang;
    }

    /**
     * Set feed title
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     * @throws Writer\Exception\InvalidArgumentException
     */
    // @codingStandardsIgnoreStart
    protected function _setTitle(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        if (! $this->getDataContainer()->getTitle()) {
            $message = 'RSS 2.0 feed elements MUST contain exactly one'
            . ' title element but a title has not been set';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (! $this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $title = $dom->createElement('title');
        $root->appendChild($title);
        $text = $dom->createTextNode($this->getDataContainer()->getTitle());
        $title->appendChild($text);
    }

    /**
     * Set feed description
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     * @throws Writer\Exception\InvalidArgumentException
     */
    // @codingStandardsIgnoreStart
    protected function _setDescription(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        if (! $this->getDataContainer()->getDescription()) {
            $message = 'RSS 2.0 feed elements MUST contain exactly one'
            . ' description element but one has not been set';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (! $this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }
        $subtitle = $dom->createElement('description');
        $root->appendChild($subtitle);
        $text = $dom->createTextNode($this->getDataContainer()->getDescription());
        $subtitle->appendChild($text);
    }


    /**
     * Set link to feed
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     * @throws Writer\Exception\InvalidArgumentException
     */
    // @codingStandardsIgnoreStart
    protected function _setLink(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $value = $this->getDataContainer()->getLink();
        if (! $value) {
            $message = 'RSS 2.0 feed elements MUST contain exactly one'
            . ' link element but one has not been set';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (! $this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }
        $link = $dom->createElement('link');
        $root->appendChild($link);
        $text = $dom->createTextNode($value);
        $link->appendChild($text);
        if (! Uri::factory($value)->isValid()) {
            $link->setAttribute('isPermaLink', 'false');
        }
    }


}
