<?php

namespace Axllent\Less;

use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Assets\Storage\GeneratedAssetHandler;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Flushable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

/**
 * LESS.php CSS compiler for SilverStripe
 * ======================================
 *
 * Extension to add Less.php CSS compiling to SilverStripe
 *
 * Usage: See README.md
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */
class LessCompiler implements Flushable
{
    /**
     * Less variables
     *
     * @array
     */
    private static $variables = [];

    /**
     * Already flushed
     *
     * @var array
     */
    private static $already_flushed = false;

    /**
     * Folder name for processed css under `assets`
     *
     * @var string
     */
    private static $processed_folder = '_css';

    /**
     * Processed files
     *
     * @var array
     */
    private static $processed_files = [];

    /**
     * Other various cached values
     */
    private $config;
    private $asset_handler;
    private $file_name_filter;
    private $is_dev;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = Config::inst();

        $this->asset_handler = $this->getAssetHandler();

        $this->file_name_filter = FileNameFilter::create();

        $this->is_dev = Director::isDev();
    }

    /**
     * Return cache directory
     */
    private static function _getCacheDir(): string
    {
        return TEMP_FOLDER . '/less-cache';
    }

    /**
     * Gets the default backend storage for generated files
     */
    public function getAssetHandler(): GeneratedAssetHandler
    {
        return Injector::inst()->get(GeneratedAssetHandler::class);
    }

    /**
     * Triggered early in the request when a flush is requested
     * Deletes the less.php cache folder and regenerates
     *
     * @return void
     */
    public static function flush()
    {
        $css_dir = self::getProcessedCSSFolder();

        if (!self::$already_flushed && '' != $css_dir) {
            // remove /public/assets/_css
            $ah = Injector::inst()->get(GeneratedAssetHandler::class);
            $ah->removeContent($css_dir);

            // remove /tmp build dir
            if (file_exists(self::_getCacheDir())) {
                $paths = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        self::_getCacheDir(),
                        \FilesystemIterator::SKIP_DOTS
                    ),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($paths as $path) {
                    $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
                }
            }
            // make sure we only flush once per request and not for each *.less
            self::$already_flushed = true;
        }
    }

    /**
     * Register the given stylesheet into the list of requirements.
     * Processes *.scss files if detected and rewrites URLs
     *
     * @param string $file    The CSS file to load, relative to site root
     * @param string $media   Media types (e.g. 'screen,projector')
     * @param array  $options list of options
     *
     * @return void
     */
    public function process($file, $media = null, $options = []): string
    {
        $file = ModuleResourceLoader::singleton()->resolvePath($file);

        return $this->processLessFile($file);
    }

    /**
     * Process less file (if detected) and return new URL
     *
     * @param string $file Original file
     */
    public function processLessFile($file): string
    {
        if (!preg_match('/\.less$/', $file)) { // Not a less file
            return $file;
        }
        if (!empty(self::$processed_files[$file])) { // already processed
            return self::$processed_files[$file];
        }

        $less_file = $file;

        // return if not a file
        if (!is_file(Director::getAbsFile($less_file))) {
            self::$processed_files[$file] = $file;

            return $file;
        }

        // Generate new CSS filename including original path to avoid conflicts.
        // eg: themes/site/css/file.less becomes themes-site-css-file.css
        $url_friendly_css_name = $this->file_name_filter->filter(
            str_replace('/', '-', preg_replace('/\.less$/i', '', $less_file))
        ) . '.css';

        $css_file = self::getProcessedCSSFolder() . '/' . $url_friendly_css_name;

        $output_file = $this->asset_handler->getContentURL($css_file);

        if (is_null($output_file) || $this->is_dev || isset($_GET['flushstyles'])) {
            $cache_dir = self::_getCacheDir();

            // relative links in css
            $less_base = dirname(Director::baseURL() . $less_file) . '/';

            $options = [
                'cache_dir' => $cache_dir,
                'compress'  => Director::isLive(), // compress CSS if live
            ];

            $current_raw_css = $this->asset_handler->getContent($css_file);

            // Generate and return compiled/cached file path
            $variables = $this->config->get('Axllent\\Less\\LessCompiler', 'variables');

            $cached_file = $cache_dir . '/' . \Less_Cache::Get(
                [Director::getAbsFile($less_file) => $less_base],
                $options,
                $variables
            );

            if (is_null($output_file) || md5($current_raw_css) != md5_file($cached_file)) {
                $this->asset_handler->setContent($css_file, file_get_contents($cached_file));
            }

            $output_file = $this->asset_handler->getContentURL($css_file);
        }

        $parsed_file = Director::makeRelative($output_file);

        self::$processed_files[$file]        = $parsed_file;
        self::$processed_files[$parsed_file] = $parsed_file;

        return $parsed_file;
    }

    /**
     * Return the processed CSS folder name
     */
    public static function getProcessedCSSFolder(): string
    {
        return Config::inst()->get(
            __CLASS__,
            'processed_folder'
        );
    }
}
