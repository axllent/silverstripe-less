<?php
Requirements::set_backend(new LessCompiler());

/* Add default ThemeDir variable */
LessCompiler::addVariable('ThemeDir', '"' . Director::baseURL() . SSViewer::get_theme_folder() . '"');

/* Set default cache directory */
LessCompiler::setCacheDir(TEMP_FOLDER . '/less-cache');

/* Set default cache method */
LessCompiler::setCacheMethod('serialize');