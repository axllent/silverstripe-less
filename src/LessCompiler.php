<?php

namespace Axllent\Less;

// use FilesystemIterator;
// use RecursiveDirectoryIterator;
// use RecursiveIteratorIterator;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Control\Director;
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

class LessCompiler extends Requirements_Backend
{

    protected static $variables = array();

    protected static $cache_dir;

    protected static $cache_method = 'serialize';

    protected static $already_flushed = false;

    protected $processed_files = [];

    public function __construct()
    {
        $this->asset_handler = $this->getAssetHandler();

        $this->file_filter = FileNameFilter::create();
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

    public function getCacheDir()
    {
        return (self::$cache_dir) ? self::$cache_dir : TEMP_FOLDER . '/less-cache';
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

        $css_file = $this->processLessFile($file);
        return parent::css($css_file, $media);
    }

    public function combineFiles($combinedFileName, $files, $options = array())
    {
        $output = [];

        foreach ($files as $file) {
            $output[] = $this->processLessFile($file);
        }

        return parent::combineFiles($combinedFileName, $output, $options);
    }

    public function processLessFile($file)
    {
        echo $file;
        if (!empty($this->processed_files[$file])) {
            echo "Processed " . $this->processed_files[$file] . ' already<br />';
            return $this->processed_files[$file];
        }

        print_r($this->processed_files);


        if (!preg_match('/\.(css|less)$/', $file)) {
            $this->processed_files[$file] = $file; // set so we don't re-process files
            return $file;
        }

        $less_file = preg_replace('/\.css$/i', '.less', $file);

        if (!is_file(Director::getAbsFile($less_file))) {
            return $file;
        }


        $css_file = str_replace('/', '-', preg_replace('/\.less$/i', '', $less_file));

        $css_file = $this->getCombinedFilesFolder() . '/' . $this->file_filter->filter($css_file) . '.css';

        $output_file = $this->asset_handler->getContentURL($css_file);

        if (is_null($output_file) || !Director::isDev()) {

            $cache_dir = $this->getCacheDir();

            $less_base = dirname(Director::baseURL() . $less_file) . '/'; // generate relative links in css

            /* Set cache directory */
            $options = [
                'cache_dir' => $cache_dir,
                'cache_method' => self::$cache_method
            ];

            $current_raw_css = $this->asset_handler->getContent($css_file);

            /* Generate and return compiled/cached file path */
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

        $this->processed_files[$file] = $parsed_file;

        return $parsed_file;
    }

}
