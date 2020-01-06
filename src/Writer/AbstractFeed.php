<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer;

use DateTime;
use DateTimeInterface;
use Mf\FeedYaTurbo\Uri;
use \Validator;

class AbstractFeed
{
    /**
     * Contains all Feed level date to append in feed output
     *
     * @var array
     */
    protected $data = [];

    /**
     * Holds the value "atom" or "rss" depending on the feed type set when
     * when last exported.
     *
     * @var string
     */
    protected $type = null;

    /**
     * @var $extensions
     */
    protected $extensions;

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
     * Set the feed description
     *
     * @param string $description
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
     */
    public function setDescription($description)
    {
        if (empty($description) || ! is_string($description)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['description'] = $description;

        return $this;
    }

    /**
    * добавление метрик на турбо страницы
    * https://yandex.ru/dev/turbo/doc/settings/analytics-docpage/#analytics__rss-1
    * на входе массив с ключами:type,id,params - не обязательный
     */
    public function addAnalytics(array $analytics)
    {
        if (empty($analytics["type"]) || ! is_string($analytics["type"])) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "type" must be a non-empty string');
        }
        if (empty($analytics["id"])) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "id" must be a non-empty string');
        }
        if (!empty($analytics["params"]) && !is_string($analytics["params"])) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "params" must be a non-empty string');
        }

        $this->data['analytics'][] = $analytics;

        return $this;
    }

    public function getAnalytics()
    {
        if (! array_key_exists('analytics', $this->data)) {
            return;
        }
        return $this->data['analytics'];
    }

    /**
    * добавление рекламных сетей на турбо страницы
    на входе массив с ключами:type,turbo-ad-id,content - не обязательный
     */
    public function addNetwork(array $network)
    {
        if (empty($network["type"]) || ! is_string($network["type"])) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "type" must be a non-empty string');
        }
        if (!in_array($network["type"],["AdFox","Yandex"])) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "type"');
        }
        
        if (empty($network["turbo-ad-id"]) || ! is_string($network["turbo-ad-id"])) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "turbo-ad-id" must be a non-empty string');
        }

        $this->data['network'][] = $network;

        return $this;
    }

    public function getNetwork()
    {
        if (! array_key_exists('network', $this->data)) {
            return;
        }
        return $this->data['network'];
    }

    /**
     * Set the feed ID - URI or URN (via PCRE pattern) supported
     *
     * @param string $id
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
     * /
    public function setId($id)
    {
        // @codingStandardsIgnoreStart
        if ((empty($id) || ! is_string($id) || ! Uri::factory($id)->isValid())
            && ! preg_match("#^urn:[a-zA-Z0-9][a-zA-Z0-9\-]{1,31}:([a-zA-Z0-9\(\)\+\,\.\:\=\@\;\$\_\!\*\-]|%[0-9a-fA-F]{2})*#", $id)
            && ! $this->_validateTagUri($id)
        ) {
            // @codingStandardsIgnoreEnd
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['id'] = $id;

        return $this;
    }

    /**
     * Validate a URI using the tag scheme (RFC 4151)
     *
     * @param string $id
     * @return bool
     * /
    // @codingStandardsIgnoreStart
    protected function _validateTagUri($id)
    {
        // @codingStandardsIgnoreEnd
        if (preg_match(
            '/^tag:(?P<name>.*),(?P<date>\d{4}-?\d{0,2}-?\d{0,2}):(?P<specific>.*)(.*:)*$/',
            $id,
            $matches
        )) {
            $dvalid = false;
            $date = $matches['date'];
            $d6 = strtotime($date);
            if ((strlen($date) == 4) && $date <= date('Y')) {
                $dvalid = true;
            } elseif ((strlen($date) == 7) && ($d6 < strtotime("now"))) {
                $dvalid = true;
            } elseif ((strlen($date) == 10) && ($d6 < strtotime("now"))) {
                $dvalid = true;
            }
            $validator = new Validator\EmailAddress;
            if ($validator->isValid($matches['name'])) {
                $nvalid = true;
            } else {
                $nvalid = $validator->isValid('info@' . $matches['name']);
            }
            return $dvalid && $nvalid;
        }
        return false;
    }


    /**
     * Set the feed language
     *
     * @param string $language
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
     */
    public function setLanguage($language)
    {
        if (empty($language) || ! is_string($language)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['language'] = $language;

        return $this;
    }

    /**
     * Set a link to the HTML source
     *
     * @param string $link
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
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
     * Set the feed title
     *
     * @param string $title
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
     */
    public function setTitle($title)
    {
        if ((empty($title) && ! is_numeric($title)) || ! is_string($title)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Set the feed character encoding
     *
     * @param string $encoding
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
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
     * Set the feed's base URL
     *
     * @param string $url
     * @throws Exception\InvalidArgumentException
     * @return AbstractFeed
     */
    public function setBaseUrl($url)
    {
        if (empty($url) || ! is_string($url) || ! Uri::factory($url)->isValid()) {
            throw new Exception\InvalidArgumentException('Invalid parameter: "url" array value'
            . ' must be a non-empty string and valid URI/IRI');
        }
        $this->data['baseUrl'] = $url;

        return $this;
    }




    /**
     * Get the feed description
     *
     * @return string|null
     */
    public function getDescription()
    {
        if (! array_key_exists('description', $this->data)) {
            return;
        }
        return $this->data['description'];
    }

    /**
     * Get the feed generator entry
     *
     * @return string|null
     */
    public function getGenerator()
    {
        if (! array_key_exists('generator', $this->data)) {
            return;
        }
        return $this->data['generator'];
    }

    /**
     * Get the feed ID
     *
     * @return string|null
     */
    public function getId()
    {
        if (! array_key_exists('id', $this->data)) {
            return;
        }
        return $this->data['id'];
    }


    /**
     * Get the feed language
     *
     * @return string|null
     */
    public function getLanguage()
    {
        if (! array_key_exists('language', $this->data)) {
            return;
        }
        return $this->data['language'];
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
     * Get the feed title
     *
     * @return string|null
     */
    public function getTitle()
    {
        if (! array_key_exists('title', $this->data)) {
            return;
        }
        return $this->data['title'];
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
     * Get the feed's base url
     *
     * @return string|null
     */
    public function getBaseUrl()
    {
        if (! array_key_exists('baseUrl', $this->data)) {
            return;
        }
        return $this->data['baseUrl'];
    }


    /**
     * Resets the instance and deletes all data
     *
     * @return void
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * Set the current feed type being exported to "rss" or "atom". This allows
     * other objects to gracefully choose whether to execute or not, depending
     * on their appropriateness for the current type, e.g. renderers.
     *
     * @param string $type
     * @return AbstractFeed
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
     * Unset a specific data point
     *
     * @param string $name
     * @return AbstractFeed
     */
    public function remove($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
        return $this;
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
            } catch (Exception\BadMethodCallException $e) {
            }
        }
        throw new Exception\BadMethodCallException(
            'Method: ' . $method . ' does not exist and could not be located on a registered Extension'
        );
    }

    /**
     * Load extensions from Mf\FeedYaTurbo\Writer\Writer
     *
     * @throws Exception\RuntimeException
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _loadExtensions()
    {
        // @codingStandardsIgnoreEnd
        $all     = Writer::getExtensions();
        $manager = Writer::getExtensionManager();
        $exts    = $all['feed'];
        foreach ($exts as $ext) {
            if (! $manager->has($ext)) {
                throw new Exception\RuntimeException(
                    sprintf('Unable to load extension "%s"; could not resolve to class', $ext)
                );
            }
            $this->extensions[$ext] = $manager->get($ext);
            $this->extensions[$ext]->setEncoding($this->getEncoding());
        }
    }
}
