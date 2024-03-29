<?php

namespace Axllent\Less\Extensions;

use Axllent\Less\LessCompiler;
use SilverStripe\Admin\LeftAndMainExtension;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\View\Requirements;

/**
 * Add any rendered editor.less to TinyMCE
 */
class HTMLEditor extends LeftAndMainExtension
{
    /**
     * On before init
     *
     * @return void
     */
    public function onBeforeInit()
    {
        $asset_handler = Requirements::backend()->getAssetHandler();

        $combined_folder = LessCompiler::getProcessedCSSFolder();

        $folder = $asset_handler->getContentURL($combined_folder);

        if (!$folder) { // _combined files doesn't exist
            return;
        }

        $files = new \FilesystemIterator(
            Director::getAbsFile(Director::makeRelative($folder))
        );

        $editor_css = [];

        foreach ($files as $file) {
            $css = $file->getFilename();
            if (preg_match('/\-editor\.css$/', $css)) {
                $editor_css[] = Director::makeRelative($folder . '/' . $css);
            }
        }

        if (!count($editor_css)) {
            return; // no *-editor.css found
        }

        Config::modify()->merge(
            'SilverStripe\\Forms\\HTMLEditor\\TinyMCEConfig',
            'editor_css',
            $editor_css
        );
    }
}
