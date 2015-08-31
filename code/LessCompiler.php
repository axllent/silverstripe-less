<?php
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

class LessCompiler extends Requirements_Backend {

	protected static $variables = array();

	protected static $cacheDir;

	protected static $cacheMethod;

	protected static $already_flushed = false;

	/**
	 * Allow manually adding variables
	 * Automatically quotes string for LESS parsing
	 * @param $key String, $value String
	 */
	public static function addVariable($key, $value) {
		self::$variables[$key] = $value;
	}

	/**
	 * Set cache directory
	 * @param $dir String
	 */
	public static function setCacheDir($dir) {
		self::$cacheDir = $dir;
	}

	/**
	 * Set cache method (for available methods check https://github.com/oyejorge/less.php#user-content-parser-caching)
	 * @param $method String
	 */
	public static function setCacheMethod($method) {
		self::$cacheMethod = $method;
	}

	function css($file, $media = null) {

		/* Only initiate if webiste is in dev mode or a ?flush is called */
		if (preg_match('/\.less$/i', $file) || Director::isDev() || isset($_GET['flush'])) {

			/* If file is CSS, check if there is a LESS file */
			if (preg_match('/\.css$/i', $file)) {
				$less = preg_replace('/\.css$/i', '.less', $file);
				if (is_file(Director::getAbsFile($less))) {
					$file = $less;
				}
			}

			/* If less file exists, then check/compile it */
			if (preg_match('/\.less$/i', $file)) {

				$out = preg_replace('/\.less$/i', '.css', $file);

				$css_file = Director::getAbsFile($out);

				$options = array();

				/* Automatically compress if in live mode */
				if (Director::isLive()) {
					$options['compress'] = true;
				}

				try {
					/* Force recompile & only write to css if updated */
					if (isset($_GET['flush']) || !Director::isLive()) {

						/* Force deleting of all cache files on flush */
						if (file_exists(self::$cacheDir) && isset($_GET['flush']) && !self::$already_flushed) {
							$paths = new RecursiveIteratorIterator(
								new RecursiveDirectoryIterator(self::$cacheDir, FilesystemIterator::SKIP_DOTS),
								RecursiveIteratorIterator::CHILD_FIRST
							);
							foreach($paths as $path) {
								$path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
							}
							/* make sure we only flush once per request and not for each *.less */
							self::$already_flushed = true;
						}

						/* Set cache directory */
						$options['cache_dir'] = self::$cacheDir;

						/* Set cache method */
						$options['cache_method'] = self::$cacheMethod;

						/* Calculate the LESS file's parent URL */
						$css_dir = dirname(Director::baseURL() . $file) . '/';

						/* Generate and return cached file path */
						$cached_file = self::$cacheDir . '/' . Less_Cache::Get(
							array(
								Director::getAbsFile($file) => $css_dir
							),
							$options,
							self::$variables
						);

						/* check cache vs. css and overwrite if necessary */
						if (!is_file($css_file) || md5_file($css_file) != md5_file($cached_file)) {
							copy($cached_file, $css_file);
						}

					}
				}
				catch (Exception $ex) {
					trigger_error('Less.php fatal error: ' . $ex->getMessage(), E_USER_ERROR);
				}

				$file = $out;
			}

		}

		/* Return css file path */
		return parent::css($file, $media);
	}

}