<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer\Renderer\Entry;

use DateTime;
use DOMDocument;
use DOMElement;
use DOMAttr;
use Mf\FeedYaTurbo\Uri;
use Mf\FeedYaTurbo\Writer;
use Mf\FeedYaTurbo\Writer\Renderer;

/**
*/
class Rss extends Renderer\AbstractRenderer implements Renderer\RendererInterface
{
    /**
     * Constructor
     *
     * @param  Writer\Entry $container
     */
    public function __construct(Writer\Entry $container)
    {
        parent::__construct($container);
    }

    /**
     * Render RSS entry
     *
     * @return Rss
     */
    public function render()
    {
        $this->dom = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput = true;
        $this->dom->substituteEntities = false;
        $entry = $this->dom->createElement('item');
        $entry->setAttributeNode(new DOMAttr('turbo', "true"));
        $this->dom->appendChild($entry);

        $this->_setDateCreated($this->dom, $entry);
        $this->_setDateModified($this->dom, $entry);
        $this->_setLink($this->dom, $entry);
        $this->_setAuthors($this->dom, $entry);
        $this->_setCategories($this->dom, $entry);
        foreach ($this->extensions as $ext) {
            $ext->setType($this->getType());
            $ext->setRootElement($this->getRootElement());
            $ext->setDomDocument($this->getDomDocument(), $entry);
            $ext->render();
        }

        return $this;
    }

    
/**
     * Set date entry was last modified
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setDateModified(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        if (! $this->getDataContainer()->getDateModified()) {
            return;
        }

        $updated = $dom->createElement('pubDate');
        $root->appendChild($updated);
        $text = $dom->createTextNode(
            $this->getDataContainer()->getDateModified()->format(DateTime::RSS)
        );
        $updated->appendChild($text);
    }

    /**
     * Set date entry was created
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setDateCreated(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        if (! $this->getDataContainer()->getDateCreated()) {
            return;
        }
        if (! $this->getDataContainer()->getDateModified()) {
            $this->getDataContainer()->setDateModified(
                $this->getDataContainer()->getDateCreated()
            );
        }
    }

    /**
     * Set entry authors
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setAuthors(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $authors = $this->container->getAuthors();
        if ((! $authors || empty($authors))) {
            return;
        }
        foreach ($authors as $data) {
            $author = $this->dom->createElement('author');
            $name = $data['name'];
            if (array_key_exists('email', $data)) {
                $name = $data['email'] . ' (' . $data['name'] . ')';
            }
            $text = $dom->createTextNode($name);
            $author->appendChild($text);
            $root->appendChild($author);
        }
    }

    /**
     * Set link to entry
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setLink(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        if (! $this->getDataContainer()->getLink()) {
            return;
        }
        $link = $dom->createElement('link');
        $root->appendChild($link);
        $text = $dom->createTextNode($this->getDataContainer()->getLink());
        $link->appendChild($text);
    }

   

    /**
     * Set entry categories
     *
     * @param DOMDocument $dom
     * @param DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setCategories(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $categories = $this->getDataContainer()->getCategories();
        if (! $categories) {
            return;
        }
        foreach ($categories as $cat) {
            $category = $dom->createElement('category',$cat['term']);
            if (isset($cat['scheme'])) {
                $category->setAttribute('domain', $cat['scheme']);
            }
            $root->appendChild($category);
        }
    }
}
