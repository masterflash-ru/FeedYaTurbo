<?php
/**
 */

namespace Mf\FeedYaTurbo\Writer;

/**
*/
class Writer
{

    /**
     * @var ExtensionManagerInterface
     */
    protected static $extensionManager = null;

    /**
     * Array of registered extensions by class postfix (after the base class
     * name) across four categories - data containers and renderers for entry
     * and feed levels.
     *
     * @var array
     */
    protected static $extensions = [
        'entry'         => [],
        'feed'          => [],
        'entryRenderer' => [],
        'feedRenderer'  => [],
    ];

    /**
     * Set plugin loader for use with Extensions
     *
     * @param ExtensionManagerInterface
     */
    public static function setExtensionManager(ExtensionManagerInterface $extensionManager)
    {
        static::$extensionManager = $extensionManager;
    }

    /**
     * Get plugin manager for use with Extensions
     *
     * @return ExtensionManagerInterface
     */
    public static function getExtensionManager()
    {
        if (! isset(static::$extensionManager)) {
            static::setExtensionManager(new ExtensionManager());
        }
        return static::$extensionManager;
    }

    /**
     * Register an Extension by name
     *
     * @param  string $name
     * @return void
     * @throws Exception\RuntimeException if unable to resolve Extension class
     */
    public static function registerExtension($name)
    {
        if (! static::hasExtension($name)) {
            throw new Exception\RuntimeException(sprintf(
                'Could not load extension "%s" using Plugin Loader.'
                . ' Check prefix paths are configured and extension exists.',
                $name
            ));
        }

        if (static::isRegistered($name)) {
            return;
        }

        $manager = static::getExtensionManager();

        $feedName = $name . '\Feed';
        if ($manager->has($feedName)) {
            static::$extensions['feed'][] = $feedName;
        }

        $entryName = $name . '\Entry';
        if ($manager->has($entryName)) {
            static::$extensions['entry'][] = $entryName;
        }

        $feedRendererName = $name . '\Renderer\Feed';
        if ($manager->has($feedRendererName)) {
            static::$extensions['feedRenderer'][] = $feedRendererName;
        }

        $entryRendererName = $name . '\Renderer\Entry';
        if ($manager->has($entryRendererName)) {
            static::$extensions['entryRenderer'][] = $entryRendererName;
        }
    }

    /**
     * Is a given named Extension registered?
     *
     * @param  string $extensionName
     * @return bool
     */
    public static function isRegistered($extensionName)
    {
        $feedName          = $extensionName . '\Feed';
        $entryName         = $extensionName . '\Entry';
        $feedRendererName  = $extensionName . '\Renderer\Feed';
        $entryRendererName = $extensionName . '\Renderer\Entry';
        if (in_array($feedName, static::$extensions['feed'])
            || in_array($entryName, static::$extensions['entry'])
            || in_array($feedRendererName, static::$extensions['feedRenderer'])
            || in_array($entryRendererName, static::$extensions['entryRenderer'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of extensions
     *
     * @return array
     */
    public static function getExtensions()
    {
        return static::$extensions;
    }

    /**
     * Reset class state to defaults
     *
     * @return void
     */
    public static function reset()
    {
        static::$extensionManager = null;
        static::$extensions   = [
            'entry'         => [],
            'feed'          => [],
            'entryRenderer' => [],
            'feedRenderer'  => [],
        ];
    }

    /**
     * Register core (default) extensions
     *
     * @return void
     */
    public static function registerCoreExtensions()
    {
        static::registerExtension('Turbo');
    }

    /**
     * @deprecated This method is deprecated and will be removed with version 3.0
     *     Use PHP's lcfirst function instead. @see https://php.net/manual/function.lcfirst.php
     * @param string $str
     * @return string
     */
    public static function lcfirst($str)
    {
        return lcfirst($str);
    }

    /**
     * Does the extension manager have the named extension?
     *
     * This method exists to allow us to test if an extension is present in the
     * extension manager. It may be used by registerExtension() to determine if
     * the extension has items present in the manager, or by
     * registerCoreExtension() to determine if the core extension has entries
     * in the extension manager. In the latter case, this can be useful when
     * adding new extensions in a minor release, as custom extension manager
     * implementations may not yet have an entry for the extension, which would
     * then otherwise cause registerExtension() to fail.
     *
     * @param string $name
     * @return bool
     */
    protected static function hasExtension($name)
    {
        $manager   = static::getExtensionManager();

        $feedName          = $name . '\Feed';
        $entryName         = $name . '\Entry';
        $feedRendererName  = $name . '\Renderer\Feed';
        $entryRendererName = $name . '\Renderer\Entry';

        return $manager->has($feedName)
            || $manager->has($entryName)
            || $manager->has($feedRendererName)
            || $manager->has($entryRendererName);
    }
}
