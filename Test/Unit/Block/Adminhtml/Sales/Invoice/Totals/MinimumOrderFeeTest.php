<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 01.04.19
 * Time: 11:22
 */

namespace Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml\Sales\Invoice\Totals;

use Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Form as InvoiceBlock;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Totals;
use Magento\Sales\Model\Order\Invoice;
use Netzexpert\MinimumOrderFee\Block\Adminhtml\Sales\Order\Invoice\Totals\MinimumOrderFee;
use Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml\GenericTotalsTest;

class MinimumOrderFeeTest extends GenericTotalsTest
{
    /** @var Totals*/
    protected $parentBlock;

    /** @var InvoiceBlock | \PHPUnit_Framework_MockObject_MockObject */
    protected $invoiceBlock;


    protected function setUp()
    {
        $this->classToTest = MinimumOrderFee::class;
        $this->parentBlockClassName = Totals::class;
        $this->invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->invoiceBlock = $this->getMockBuilder(InvoiceBlock::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->invoiceBlock->expects($this->any())
            ->method('getInvoice')
            ->willReturn($this->invoiceMock);
        $this->blockCallback = function ($name) {
            if ($name == 'totals') {
                return $this->parentBlock;
            }
            return $this->invoiceBlock;
        };
        parent::setUp();
    }

    protected function getParentBlock($class)
    {
        $block = $this->getTotalsBlock($class)->setLayout($this->layoutMock)->setNameInLayout('invoice_totals');
        $block->toHtml();
        return $block;
    }

}
