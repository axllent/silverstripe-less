<?php

namespace Axllent\Less;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Control\Director;
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

    protected static $variables = array();

    protected static $cache_dir = TEMP_FOLDER . '/less-cache';

    protected static $cache_method = 'serialize';

    protected static $already_flushed = false;

    protected static $processed_files = [];

    public function __construct()
    {
        $this->asset_handler = $this->getAssetHandler();

        $this->file_name_filter = FileNameFilter::create();

        $this->is_dev = Director::isDev();
    }

    /**
     * Triggered early in the request when a flush is requested
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
            /* make sure we only flush once per request and not for each *.less */
            self::$already_flushed = true;
        }
    }

    /**
     * Allow manually adding variables
     * Automatically quotes string for LESS parsing
     * @param $key String, $value String
     */
    public static function addVariable($key, $value)
    {
        self::$variables[$key] = $value;
    }

    /**
     * Set cache directory
     * @param $dir String
     */
    public static function setCacheDir($dir)
    {
        self::$cache_dir = rtrim($dir, '/');
    }

    public static function getCacheDir()
    {
        return TEMP_FOLDER . '/less-cache';
    }

    /**
     * Set cache method (for available methods check https://github.com/oyejorge/less.php#user-content-parser-caching)
     * @param $method String
     */
    public static function setCacheMethod($method)
    {
        self::$cache_method = $method;
    }

    /**
     * Gets the default backend storage for generated files
     *
     * @return GeneratedAssetHandler
     */
    public function getAssetHandler()
    {
        return Injector::inst()->get('GeneratedAssetHandler');
    }

    public function css($file, $media = null)
    {
        $css_file = $this->processIfLessFile($file);
        return parent::css($css_file, $media);
    }

    /**
     * Process any less files and return new filenames
     * See Requirements_Backend->combineFiles() for options
     */
    public function combineFiles($combinedFileName, $files, $options = array())
    {
        $new_files = [];

        foreach ($files as $file) {
            $new_files[] = $this->processIfLessFile($file);
        }

        return parent::combineFiles($combinedFileName, $new_files, $options);
    }

    /**
     * Process less file (if detected)
     * @param String (original)
     * @return String (new filename)
     */
    public function processIfLessFile($file)
    {
        // don't re-process files
        if (!empty(self::$processed_files[$file])) {
            return self::$processed_files[$file];
        }

        if (!preg_match('/\.(css|less)$/', $file)) {
            self::$processed_files[$file] = $file; // set so we don't re-process files
            return $file;
        }

        $less_file = preg_replace('/\.css$/i', '.less', $file);

        if (!is_file(Director::getAbsFile($less_file))) {
            return $file;
        }


        $css_file = str_replace('/', '-', preg_replace('/\.less$/i', '', $less_file));

        $css_file = $this->getCombinedFilesFolder() . '/' . $this->file_name_filter->filter($css_file) . '.css';

        $output_file = $this->asset_handler->getContentURL($css_file);

        if (is_null($output_file) || $this->is_dev) {

            $cache_dir = self::getCacheDir();

            $less_base = dirname(Director::baseURL() . $less_file) . '/'; // generate relative links in css

            // Set less options
            $options = [
                'cache_dir' => $cache_dir,
                'cache_method' => self::$cache_method,
                'compress' => Director::isLive() // compress if live
            ];

            $current_raw_css = $this->asset_handler->getContent($css_file);

            // Generate and return compiled/cached file path
            $cached_file = $cache_dir . '/' . \Less_Cache::Get(
                array(
                    Director::getAbsFile($less_file) => $less_base
                ),
                $options,
                self::$variables
            );

            if (is_null($output_file) || md5($current_raw_css) != md5_file($cached_file)) {
                $this->asset_handler->setContent($css_file, file_get_contents($cached_file));
            }

            $output_file = $this->asset_handler->getContentURL($css_file);
        }

        $parsed_file = Director::makeRelative($output_file);

        self::$processed_files[$file] = $parsed_file;
        self::$processed_files[$parsed_file] = $parsed_file;

        return $parsed_file;
    }

}
