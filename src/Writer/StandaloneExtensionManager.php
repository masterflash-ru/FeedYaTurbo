<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer;

use Mf\FeedYaTurbo\Writer\Exception\InvalidArgumentException;

class StandaloneExtensionManager implements ExtensionManagerInterface
{
    private $extensions = [
        'Turbo\Renderer\Feed'       => Extension\Turbo\Renderer\Feed::class,
        'Turbo\Renderer\Entry'       => Extension\Turbo\Renderer\Entry::class,
    ];

    /**
     * Do we have the extension?
     *
     * @param  string $extension
     * @return bool
     */
    public function has($extension)
    {
        return array_key_exists($extension, $this->extensions);
    }

    /**
     * Retrieve the extension
     *
     * @param  string $extension
     * @return mixed
     */
    public function get($extension)
    {
        $class = $this->extensions[$extension];
        return new $class();
    }

    /**
     * Add an extension.
     *
     * @param string $name
     * @param string $class
     */
    public function add($name, $class)
    {
        if (is_string($class)
            && ((
                is_a($class, Extension\AbstractRenderer::class, true)
                || 'Feed' === substr($class, -4)
                || 'Entry' === substr($class, -5)
            ))
        ) {
            $this->extensions[$name] = $class;

            return;
        }

        throw new InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Extension\RendererInterface '
            . 'or the classname must end in "Feed" or "Entry"',
            $class,
            __NAMESPACE__
        ));
    }

    /**
     * Remove an extension.
     *
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->extensions[$name]);
    }
}
