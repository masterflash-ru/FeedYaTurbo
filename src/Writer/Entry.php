<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mf\FeedYaTurbo\Writer;

use DateTime;
use DateTimeInterface;
use Mf\FeedYaTurbo\Uri;

/**
*/
class Entry
{
    /**
     * Internal array containing all data associated with this entry or item.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Registered extensions
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * Holds the value "atom" or "rss" depending on the feed type set when
     * when last exported.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Constructor: Primarily triggers the registration of core extensions and
     * loads those appropriate to this data container.
     *
     */
    public function __construct()
    {
        Writer::registerCoreExtensions();
        $this->_loadExtensions();
    }

    /**
     * Set a single author
     *
     * The following option keys are supported:
     * 'name'  => (string) The name
     * 'email' => (string) An optional email
     * 'uri'   => (string) An optional and valid URI
     *
     * @param array $author
     * @throws Exception\InvalidArgumentException If any value of $author not follow the format.
     * @return Entry
     */
    public function addAuthor(array $author)
    {
        // Check array values
        if (! array_key_exists('name', $author)
            || empty($author['name'])
            || ! is_string($author['name'])
        ) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: author array must include a "name" key with a non-empty string value'
            );
        }

        if (isset($author['email'])) {
            if (empty($author['email']) || ! is_string($author['email'])) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "email" array value must be a non-empty string'
                );
            }
        }
        if (isset($author['uri'])) {
            if (empty($author['uri']) || ! is_string($author['uri']) ||
                ! Uri::factory($author['uri'])->isValid()
            ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "uri" array value must be a non-empty string and valid URI/IRI'
                );
            }
        }

        $this->data['authors'][] = $author;

        return $this;
    }

    /**
     * Set an array with feed authors
     *
     * @see addAuthor
     * @param array $authors
     * @return Entry
     */
    public function addAuthors(array $authors)
    {
        foreach ($authors as $author) {
            $this->addAuthor($author);
        }

        return $this;
    }

    /**
     * Set the feed character encoding
     *
     * @param string $encoding
     * @throws Exception\InvalidArgumentException
     * @return Entry
     */
    public function setEncoding($encoding)
    {
        if (empty($encoding) || ! is_string($encoding)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['encoding'] = $encoding;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource()
    {
        if (! array_key_exists('source', $this->data)) {
            return;
        }
        return $this->data['source'];
    }

    /**
     */
    public function setSource($link)
    {
        if (empty($link) || ! is_string($link) || ! Uri::factory($link)->isValid()) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['source'] = $link;

        return $this;
    }

    /**
     * Get the feed character encoding
     *
     * @return string|null
     */
    public function getEncoding()
    {
        if (! array_key_exists('encoding', $this->data)) {
            return 'UTF-8';
        }
        return $this->data['encoding'];
    }

    /**
     * Set the entry's content
     *
     * @param string $content
     * @throws Exception\InvalidArgumentException
     * @return Entry
     */
    public function setContent($content)
    {
        if (empty($content) || ! is_string($content)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['content'] = $content;

        return $this;
    }

    /**
     * Set the feed creation date
     *
     * @param null|int|DateTimeInterface $date
     * @throws Exception\InvalidArgumentException
     * @return Entry
     */
    public function setDateCreated($date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        }
        if (is_int($date)) {
            $date = new DateTime('@' . $date);
        }
        if (! $date instanceof DateTimeInterface) {
            throw new Exception\InvalidArgumentException(
                'Invalid DateTime object or UNIX Timestamp passed as parameter'
            );
        }
        $this->data['dateCreated'] = $date;

        return $this;
    }

    /**
     * Set the feed modification date
     *
     * @param null|int|DateTimeInterface $date
     * @throws Exception\InvalidArgumentException
     * @return Entry
     */
    public function setDateModified($date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        }
        if (is_int($date)) {
            $date = new DateTime('@' . $date);
        }
        if (! $date instanceof DateTimeInterface) {
            throw new Exception\InvalidArgumentException(
                'Invalid DateTime object or UNIX Timestamp passed as parameter'
            );
        }
        $this->data['dateModified'] = $date;

        return $this;
    }


    /**
     * Set the feed ID
     *
     * @param string $id
     * @throws Exception\InvalidArgumentException
     * @return Entry
     * /
    public function setId($id)
    {
        if (empty($id) || ! is_string($id)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['id'] = $id;

        return $this;
    }

    /**
     * Set a link to the HTML source of this entry
     *
     * @param string $link
     * @throws Exception\InvalidArgumentException
     * @return Entry
     */
    public function setLink($link)
    {
        if (empty($link) || ! is_string($link) || ! Uri::factory($link)->isValid()) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['link'] = $link;

        return $this;
    }

    


    /**
     * Get an array with feed authors
     *
     * @return array
     */
    public function getAuthors()
    {
        if (! array_key_exists('authors', $this->data)) {
            return;
        }
        return $this->data['authors'];
    }

    /**
     * Get the entry content
     *
     * @return string
     */
    public function getContent()
    {
        if (! array_key_exists('content', $this->data)) {
            return;
        }
        return $this->data['content'];
    }


    /**
     * Get the entry creation date
     *
     * @return string
     */
    public function getDateCreated()
    {
        if (! array_key_exists('dateCreated', $this->data)) {
            return;
        }
        return $this->data['dateCreated'];
    }

    /**
     * Get the entry modification date
     *
     * @return string
     */
    public function getDateModified()
    {
        if (! array_key_exists('dateModified', $this->data)) {
            return;
        }
        return $this->data['dateModified'];
    }


    /**
     * Get the entry ID
     *
     * @return string
     */
    public function getId()
    {
        if (! array_key_exists('id', $this->data)) {
            return;
        }
        return $this->data['id'];
    }

    /**
     * Get a link to the HTML source
     *
     * @return string|null
     */
    public function getLink()
    {
        if (! array_key_exists('link', $this->data)) {
            return;
        }
        return $this->data['link'];
    }

    


    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks()
    {
        if (! array_key_exists('links', $this->data)) {
            return;
        }
        return $this->data['links'];
    }

    /**
     * Get the entry title
     *
     * @return string
     */
    public function getTitle()
    {
        if (! array_key_exists('title', $this->data)) {
            return;
        }
        return $this->data['title'];
    }



    /**
     * Add an entry category
     *
     * @param array $category
     * @throws Exception\InvalidArgumentException
     * @return Entry
     */
    public function addCategory(array $category)
    {
        if (! isset($category['term'])) {
            throw new Exception\InvalidArgumentException('Each category must be an array and '
            . 'contain at least a "term" element containing the machine '
            . ' readable category name');
        }
        if (isset($category['scheme'])) {
            if (empty($category['scheme'])
                || ! is_string($category['scheme'])
                || ! Uri::factory($category['scheme'])->isValid()
            ) {
                throw new Exception\InvalidArgumentException('The Atom scheme or RSS domain of'
                . ' a category must be a valid URI');
            }
        }
        if (! isset($this->data['categories'])) {
            $this->data['categories'] = [];
        }
        $this->data['categories'][] = $category;

        return $this;
    }

    /**
     * Set an array of entry categories
     *
     * @param array $categories
     * @return Entry
     */
    public function addCategories(array $categories)
    {
        foreach ($categories as $category) {
            $this->addCategory($category);
        }

        return $this;
    }

    /**
     * Get the entry categories
     *
     * @return string|null
     */
    public function getCategories()
    {
        if (! array_key_exists('categories', $this->data)) {
            return;
        }
        return $this->data['categories'];
    }



    /**
     * Unset a specific data point
     *
     * @param string $name
     * @return Entry
     */
    public function remove($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }

        return $this;
    }

    /**
     * Get registered extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Return an Extension object with the matching name (postfixed with _Entry)
     *
     * @param string $name
     * @return object
     */
    public function getExtension($name)
    {
        if (array_key_exists($name . '\\Entry', $this->extensions)) {
            return $this->extensions[$name . '\\Entry'];
        }
        return;
    }

    /**
     * Set the current feed type being exported to "rss" or "atom". This allows
     * other objects to gracefully choose whether to execute or not, depending
     * on their appropriateness for the current type, e.g. renderers.
     *
     * @param string $type
     * @return Entry
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve the current or last feed type exported.
     *
     * @return string Value will be "rss" or "atom"
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Method overloading: call given method on first extension implementing it
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException if no extensions implements the method
     */
    public function __call($method, $args)
    {
        foreach ($this->extensions as $extension) {
            try {
                return call_user_func_array([$extension, $method], $args);
            } catch (\BadMethodCallException $e) {
            }
        }
        throw new Exception\BadMethodCallException('Method: ' . $method
            . ' does not exist and could not be located on a registered Extension');
    }



    /**
     * Load extensions from Mf\FeedYaTurbo\Writer\Writer
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _loadExtensions()
    {
        // @codingStandardsIgnoreEnd
        $all     = Writer::getExtensions();
        $manager = Writer::getExtensionManager();
        $exts    = $all['entry'];
        foreach ($exts as $ext) {
            $this->extensions[$ext] = $manager->get($ext);
            $this->extensions[$ext]->setEncoding($this->getEncoding());
        }
    }
}
