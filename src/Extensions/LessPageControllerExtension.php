<?php
namespace Axllent\Less\Extensions;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class LessPageControllerExtension extends Extension
{
    /**
     * Do not combine requirements if it is an error page
     */
    public function onBeforeInit()
    {
        if (ClassInfo::hasMethod($this->owner, 'get_error_filename') &&
            !empty(Classinfo::ancestry($this->owner)['silverstripe\errorpage\errorpagecontroller'])
        ) {
            Requirements::set_combined_files_enabled(false);
        }
    }
}
