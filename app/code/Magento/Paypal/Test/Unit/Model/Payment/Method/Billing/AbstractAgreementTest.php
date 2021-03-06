<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Test\Unit\Model\Payment\Method\Billing;

use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Paypal\Model\Billing\Agreement;
use Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment;

class AbstractAgreementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Billing\AgreementFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $agreementFactory;

    /**
     * @var AbstractAgreementStub
     */
    private $payment;

    public function setUp()
    {
        $helper = new ObjectManager($this);

        $this->agreementFactory = $this->getMockBuilder('Magento\Paypal\Model\Billing\AgreementFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->payment = $helper->getObject(
            AbstractAgreementStub::class,
            [
                'agreementFactory' => $this->agreementFactory
            ]
        );
    }
    public function testAssignData()
    {
        $baId = '1678235';
        $customerId = 67;
        $referenceId = '1234124';

        $data = new DataObject(
            [
                PaymentInterface::KEY_ADDITIONAL_DATA => [
                    AbstractAgreement::TRANSPORT_BILLING_AGREEMENT_ID => $baId
                ]
            ]
        );

        $paymentInfo = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getCustomerId'])
            ->getMock();

        $this->payment->setInfoInstance($paymentInfo);

        $agreementModel = $this->getMockBuilder(Agreement::class)
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'load', 'getCustomerId', 'getId', 'getReferenceId'])
            ->getMock();

        $this->agreementFactory->expects(static::once())
            ->method('create')
            ->willReturn($agreementModel);

        $paymentInfo->expects(static::once())
            ->method('getQuote')
            ->willReturn($quote);

        $agreementModel->expects(static::once())
            ->method('load')
            ->with($baId);
        $agreementModel->expects(static::once())
            ->method('getId')
            ->willReturn($baId);
        $agreementModel->expects(static::atLeastOnce())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $agreementModel->expects(static::atLeastOnce())
            ->method('getReferenceId')
            ->willReturn($referenceId);

        $quote->expects(static::once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $paymentInfo->expects(static::exactly(2))
            ->method('setAdditionalInformation')
            ->willReturnMap(
                [
                    AbstractAgreement::TRANSPORT_BILLING_AGREEMENT_ID, $baId,
                    AbstractAgreement::PAYMENT_INFO_REFERENCE_ID, $referenceId
                ]
            );
        $this->payment->assignData($data);
    }
}
