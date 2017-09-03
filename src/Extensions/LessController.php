<?php
/**
 * Set backend for requirements to LessCompiler
 */

namespace Axllent\Less\Extensions;

use Axllent\Less\LessCompiler;
use SilverStripe\Core\Extension;
use SilverStripe\Control\Controller;
use SilverStripe\View\Requirements;

class LessController extends Extension
{
    public function onBeforeInit()
    {
        $backend = LessCompiler::create();
        Requirements::set_backend($backend);
    }
}
