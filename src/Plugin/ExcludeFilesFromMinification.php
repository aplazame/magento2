<?php

namespace Aplazame\Payment\Plugin;

use Magento\Framework\View\Asset\Minification;

class ExcludeFilesFromMinification
{
    public function aroundGetExcludes(Minification $subject, callable $proceed, $contentType)
    {
        $result = $proceed($contentType);
        if ($contentType != 'js' && !$subject->isEnabled($contentType)) {
            return $result;
        }
        $result[] = 'https://cdn.aplazame.com/aplazame.js';
        return $result;
    }
}
