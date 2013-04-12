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

		/* If file is CSS, check if there is a LESS file */
		if (preg_match('/\.css$/i', $file)) {
			$less = preg_replace('/\.css$/i', '.less', $file);
			if (is_file(Director::getAbsFile($less))) {
				$file = $less;
			}
		}
		/* If less file check / compile it and save to css */
		if (preg_match('/\.less$/i', $file)) {
			$compiler = 'checkedCompile';
			$out = preg_replace('/\.less$/i', '.css', $file);
			/* Force recompile if ?flush */
			if(isset($_GET['flush']))
				$compiler = 'compileFile';
			$less = new lessc;
			/* Automatically compress if in live mode */
			if (DIRECTOR::isLive())
				$less->setFormatter("compressed");
			try {
				$less->$compiler(Director::getAbsFile($file), Director::getAbsFile($out));
			} catch (Exception $ex) {
				trigger_error("lessphp fatal error: " . $ex->getMessage(), E_USER_ERROR);
			}
			$file = $out;
		}
		/* Return css file */
		return parent::css($file, $media);
	}

}