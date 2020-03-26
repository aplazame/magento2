<?php

namespace Aplazame\Payment\Plugin;

use Closure;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;

class CsrfValidatorSkip
{
    /**
     * @param CsrfValidator $subject
     * @param Closure $proceed
     * @param RequestInterface $request
     * @param ActionInterface $action
     */
    public function aroundValidate($subject, Closure $proceed, $request, $action)
    {
        if ($request->getModuleName() == 'aplazame') {
            return; // Skip CSRF check
        }
        $proceed($request, $action); // Proceed to Magento 2 core functions
    }
}
