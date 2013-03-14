<?php
/**
 * LESS CSS compiler for SilverStripe
 * ===================================
 *
 * Extension to add Less CSS compiling to SilverStripe
 *
 * Usage: See README
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
		if (preg_match('/\.less$/i', $file)) {
			$out = preg_replace('/\.less$/i', '.css', $file);
			if(isset($_GET['flush']) && Permission::check('CMS_ACCESS_CMSMain')) {
				@unlink(Director::getAbsFile($out));
			}
			lessc::ccompile(Director::getAbsFile($file), Director::getAbsFile($out));
			$file = $out;
		}
		return parent::css($file, $media);
	}

}