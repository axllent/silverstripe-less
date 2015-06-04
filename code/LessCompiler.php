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

	/*
	 * Allow manually adding variables
	 * Automatically quotes string for LESS parsing
	 * @param $key String, $value String
	 */
	public static function addVariable($key, $value) {
		self::$variables[$key] = $value;
	}

	function css($file, $media = null) {

		/**
		 * Only initiate automatically if:
		 * - webiste is in dev mode
		 * - or a ?flush is called
		 */
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

						/* Create instance */
						$parser = new Less_Parser($options);

						if (!empty(self::$variables)) {
							$parser->ModifyVars(self::$variables);
						}

						/* calculate the LESS file's parent URL */
						$css_dir = rtrim(Director::baseURL(), '/').Director::makeRelative(dirname(Director::getAbsFile($file)).'/');

						$parser->parseFile(Director::getAbsFile($file), $css_dir);

						$css = $parser->getCss();

						if (!is_file($css_file) || md5_file($css_file) != md5($css)) {
							file_put_contents($css_file, $css);
						}

					}
				}
				catch (Exception $ex) {
					trigger_error("Less.php fatal error: " . $ex->getMessage(), E_USER_ERROR);
				}

				$file = $out;
			}

		}

		/* Return css path */
		return parent::css($file, $media);
	}

}