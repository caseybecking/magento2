<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Test\Unit\Model\Payment\Method\Billing;

use Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement;

class AbstractAgreementStub extends AbstractAgreement
{
    const STUB_CODE = 'stub-code';

    /**
     * @return string
     */
    public function getCode()
    {
        return static::STUB_CODE;
    }
    
    /**
     * @param object $quote
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _isAvailable($quote)
    {
        return false;
    }
}
