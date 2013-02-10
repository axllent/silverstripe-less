<?php
class LessCompiler extends Requirements_Backend {

	function css($file, $media = null) {
		if (preg_match('/\.less$/i', $file)) {
			$out = preg_replace('/\.less$/i', '.css', $file);
			if(isset($_GET['flush']) && Permission::check('ADMIN')) {
				@unlink(Director::getAbsFile($out));
			}
			lessc::ccompile(Director::getAbsFile($file), Director::getAbsFile($out));
			$file = $out;
		}
		return parent::css($file, $media);
	}

}