<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 02.04.19
 * Time: 9:39
 */

namespace Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml\Sales\Creditmemo\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals;
use Magento\Sales\Block\Adminhtml\Order\Creditmemo\View\Form as CreditmemoBlock;
use Magento\Sales\Block\Order\Invoice;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Tax\Model\Config as TaxConfig;
use Netzexpert\MinimumOrderFee\Block\Adminhtml\Sales\Order\Creditmemo\Totals\MinimumOrderFee;
use Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml\GenericTotalsTest;

class MinimumOrderFeeTest extends GenericTotalsTest
{
    /** @var MinimumOrderFee */
    protected $model;

    /** @var TaxConfig | \PHPUnit_Framework_MockObject_MockObject */
    protected $taxConfigMock;

    /** @var PriceCurrencyInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $priceCurrency;

    protected function setUp()
    {
        $this->classToTest = MinimumOrderFee::class;
        $this->parentBlockClassName = Totals::class;
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registryCallback = function ($name) {
            switch ($name) {
                case 'current_invoice':
                    return $this->invoiceMock;
                    break;
                case 'current_creditmemo':
                    return $this->creditmemoMock;
                    break;
                default:
                    return '';
                    break;
            }
        };
        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with($this->anything())
            ->will($this->returnCallback($registryCallback));
        $this->creditmemoMock = $this->getMockBuilder(Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creditmemoBlock = $this->getMockBuilder(CreditmemoBlock::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creditmemoBlock->expects($this->any())
            ->method('getCreditmemo')
            ->willReturn($this->creditmemoMock);
        $this->blockCallback = function ($name) {
            if ($name == 'totals') {
                return $this->parentBlock;
            }
            return $this->creditmemoBlock;
        };
        $this->taxConfigMock = $this->getMockBuilder(TaxConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrency = $this->getMockBuilder(PriceCurrencyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    /**
     * @return \Magento\Sales\Block\Adminhtml\Order\Totals | Invoice | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTotalsBlock($class)
    {
        return $this->objectManager->getObject(
            $class,
            [
                $this->contextMock,
                $this->registryMock,
                $this->taxConfigMock,
                'priceCurrency' => $this->priceCurrency,
                $this->adminHelperMock
            ]
        )->setOrder($this->orderMock)->setInvoice($this->invoiceMock);
    }

    public function testFormatPrice()
    {
        $this->creditmemoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())
            ->method('formatPrice')
            ->with(20.48)
            ->willReturn("20,48€");
        $this->assertEquals("20,48€", $this->model->formatPrice(20.48));
    }

    public function testGetSource()
    {
        $this->assertEquals($this->model->getSource(), $this->creditmemoMock);
    }

    public function testGetFeeLabel()
    {
        $this->assertEquals(__('Minimum Order Fee'), $this->model->getFeeLabel());
    }

    /**
     * @param  int | bool
     * @param $feeAmount float
     * @dataProvider getFeeAmountDataProvider
     */
    public function testGetFeeAmount($id, $fee, $feeAmount)
    {
        $this->creditmemoMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $this->creditmemoMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->any())
            ->method('getData')
            ->withConsecutive(['minimum_order_fee'], ['payment_method_fee_refunded'])
            ->willReturnOnConsecutiveCalls(1.591, 1);
        $this->creditmemoMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(1.591);
        $this->priceCurrency->expects($this->any())
            ->method('round')
            ->with($fee)
            ->willReturn($feeAmount);
        $this->assertEquals($feeAmount, $this->model->getFeeAmount());
    }

    protected function getParentBlock($class)
    {
        $block = $this->getTotalsBlock($class)
            ->setLayout($this->layoutMock)
            ->setNameInLayout('creditmemo_totals')
            ->setData('creditmemo', $this->creditmemoMock);
        $block->toHtml();
        return $block;
    }

    public function getFeeAmountDataProvider()
    {
        return [
            [false, 0.591, 0.59],
            [1, 1.591, 1.59]
        ];
    }
}
