<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 01.04.19
 * Time: 10:39
 */

namespace Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml;

use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Layout;
use Magento\Sales\Block\Adminhtml\Order\Creditmemo\View\Form as CreditmemoBlock;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Form as InvoiceBlock;
use Magento\Sales\Block\Adminhtml\Order\Totals;
use Magento\Sales\Block\Order\Invoice;
use Magento\Sales\Helper\Admin;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;

abstract class GenericTotalsTest extends TestCase
{
    /** @var string */
    protected $classToTest;

    /** @var string */
    protected $parentBlockClassName;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var Context | \PHPUnit_Framework_MockObject_MockObject  */
    protected $contextMock;

    /** @var Registry | \PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var Admin | \PHPUnit_Framework_MockObject_MockObject */
    protected $adminHelperMock;

    /** @var Order | \PHPUnit_Framework_MockObject_MockObject */
    protected $orderMock;

    /** @var Order\Invoice | \PHPUnit_Framework_MockObject_MockObject */
    protected $invoiceMock;

    /** @var InvoiceBlock | \PHPUnit_Framework_MockObject_MockObject */
    protected $invoiceBlock;

    /** @var CreditmemoBlock | \PHPUnit_Framework_MockObject_MockObject */
    protected $creditmemoBlock;

    /** @var Order\Creditmemo | \PHPUnit_Framework_MockObject_MockObject */
    protected $creditmemoMock;

    /** @var Totals*/
    protected $parentBlock;

    /** @var Layout | \PHPUnit_Framework_MockObject_MockObject */
    protected $layoutMock;

    /** @var Totals */
    protected $model;

    protected $map = [
        'minimum_order_fee' => 'totals',
        'order_totals'      => 'order_tab_info',
        'invoice_totals'    => 'form',
        'creditmemo_totals' => 'form',
    ];

    /** @var callable */
    protected $blockCallback;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $this->adminHelperMock = $this->getMockBuilder(Admin::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->invoiceMock = $this->getMockBuilder(Order\Invoice::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creditmemoMock = $this->getMockBuilder(Order\Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->layoutMock = $this->getMockBuilder(Layout::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->layoutMock->expects($this->any())
            ->method('getChildBlocks')
            ->willReturn([]);
        $parentNameCallback = function ($name) {
            return (!empty($this->map[$name])) ? $this->map[$name] : '';
        };
        $this->layoutMock->expects($this->any())
            ->method('getParentName')
            ->with($this->anything())
            ->will($this->returnCallback($parentNameCallback));

        $this->model = $this->getTotalsBlock($this->classToTest)->setLayout($this->layoutMock)
            ->setNameInLayout('minimum_order_fee');
        $this->layoutMock->expects($this->any())
            ->method('getBlock')
            ->with($this->anything())
            ->will($this->returnCallback($this->blockCallback));
        $this->parentBlock = $this->getParentBlock($this->parentBlockClassName);
    }

    abstract protected function getParentBlock($class);

    /**
     * @return Totals | Invoice | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTotalsBlock($class)
    {
        return $this->objectManager->getObject(
            $class,
            [
                $this->contextMock,
                $this->registryMock,
                $this->adminHelperMock
            ]
        )->setOrder($this->orderMock)->setInvoice($this->invoiceMock);
    }

    public function testEmptyTotals()
    {
        $this->orderMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(0);
        $this->invoiceMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(0);
        $this->creditmemoMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(0);
        $this->model->initTotals();
        $this->assertArrayNotHasKey('minimum_order_fee', $this->parentBlock->getTotals());
    }

    public function testTotals()
    {
        $this->orderMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(1);
        $this->invoiceMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(1);
        $this->creditmemoMock->expects($this->any())
            ->method('getData')
            ->with('minimum_order_fee')
            ->willReturn(1);
        $this->model->initTotals();
        $this->assertArrayHasKey('minimum_order_fee', $this->parentBlock->getTotals());
    }
}
