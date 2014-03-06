<?php
/**
 * LESS CSS compiler for SilverStripe
 * ===================================
 *
 * Extension to add Less CSS compiling to SilverStripe
 *
 * Usage: See README.md
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */

class LessCompiler extends Requirements_Backend {

	function css($file, $media = null) {

		/*
		 * Only initiate automatically if:
		 * - webiste is in dev mode
		 * - or a ?flush is called
		 */
		if (Director::isDev() || isset($_GET['flush'])) {

			/* If file is CSS, check if there is a LESS file */
			if (preg_match('/\.css$/i', $file)) {
				$less = preg_replace('/\.css$/i', '.less', $file);
				if (is_file(Director::getAbsFile($less))) {
					$file = $less;
				}
			}

			/* If less file, then check/compile it */
			if (preg_match('/\.less$/i', $file)) {

				$out = preg_replace('/\.less$/i', '.css', $file);

				$css_file = Director::getAbsFile($out);

				/* Force recompile if ?flush */
				if (isset($_GET['flush'])) {
					$compiler = 'compileFile';
				}

				/* Create instance */
				$less = new lessc;

				/* Automatically compress if in live mode */
				if (Director::isLive()) {
					$less->setFormatter("compressed");
				}

				try {
					/* Force recompile & only write to css if updated */
					if (isset($_GET['flush'])) {
						$compiled = $less->compileFile(Director::getAbsFile($file));
						if (!is_file($css_file) || md5_file($css_file) != md5($compiled))
							file_put_contents($css_file, $compiled);
					} else {
						$less->checkedCompile(Director::getAbsFile($file), $css_file);
					}
				} catch (Exception $ex) {
					trigger_error("lessphp fatal error: " . $ex->getMessage(), E_USER_ERROR);
				}

				$file = $out;
			}

		}

		/* Return css path */
		return parent::css($file, $media);
	}

}
