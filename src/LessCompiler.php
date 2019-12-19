<?php
namespace Axllent\Less;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Assets\Storage\GeneratedAssetHandler;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Flushable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\Requirements_Backend;

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

class LessCompiler extends Requirements_Backend implements Flushable
{

    /**
     * @config
     */
    private static $cache_method = 'serialize';

    /**
     * @config
     */
    private static $variables = [];

    /**
     * @var mixed
     */
    private static $already_flushed = false;

    /**
     * @var array
     */
    private static $processed_files = [];

    public function __construct()
    {
        $this->config = Config::inst();

        $this->asset_handler = $this->getAssetHandler();

        $this->file_name_filter = FileNameFilter::create();

        $this->is_dev = Director::isDev();
    }

    /**
     * Return cache directory
     * @param Null
     * @return String
     */
    private static function getCacheDir()
    {
        return TEMP_FOLDER . '/less-cache';
    }

    /**
     * Gets the default backend storage for generated files
     *
     * @return GeneratedAssetHandler
     */
    public function getAssetHandler()
    {
        return Injector::inst()->get(GeneratedAssetHandler::class);
    }

    /**
     * Triggered early in the request when a flush is requested
     * Deletes the less.php cache folder and regenerates
     */
    public static function flush()
    {
        if (!self::$already_flushed && file_exists(self::getCacheDir())) {
            $paths = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(self::getCacheDir(), FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($paths as $path) {
                $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
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
     * @param array  $options List of options.
     *
     * @return void
     */
    public function css($file, $media = null, $options = [])
    {
        $css_file = $this->processLessFile($file);

        return parent::css($css_file, $media, $options);
    }

    /**
     * Process any less files and return new filenames
     * @See Requirements_Backend->combineFiles() for options
     */
    public function combineFiles($combinedFileName, $files, $options = [])
    {
        $new_files = [];

        foreach ($files as $file) {
            $new_files[] = $this->processLessFile($file);
        }

        return parent::combineFiles($combinedFileName, $new_files, $options);
    }

    /**
     * Process less file (if detected) and return new URL
     * @param String (original)
     * @return String (new filename)
     */
    public function processLessFile($file)
    {
        if (!preg_match('/\.less$/', $file)) { // Not a less file
            return $file;
        } elseif (!empty(self::$processed_files[$file])) { // already processed
            return self::$processed_files[$file];
        }

        $less_file = $file;

        // return if not a file
        if (!is_file(Director::getAbsFile($less_file))) {
            self::$processed_files[$file] = $file;

            return $file;
        }

        // Generate a new CSS filename that includes the original path to avoid naming conflicts.
        // eg: themes/site/css/file.less becomes themes-site-css-file.css
        $url_friendly_css_name = $this->file_name_filter->filter(
            str_replace('/', '-', preg_replace('/\.less$/i', '', $less_file))
        ) . '.css';

        $css_file = $this->getCombinedFilesFolder() . '/' . $url_friendly_css_name;

        $output_file = $this->asset_handler->getContentURL($css_file);

        if (is_null($output_file) || $this->is_dev) {
            $cache_dir = self::getCacheDir();

            // relative links in css
            $less_base = dirname(Director::baseURL() . $less_file) . '/';

            // Set less options
            $cache_method = $this->config->get('Axllent\\Less\\LessCompiler', 'cache_method');

            $options = [
                'cache_dir'    => $cache_dir,
                'cache_method' => $cache_method,
                'compress'     => Director::isLive(), // compress CSS if live
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
}
