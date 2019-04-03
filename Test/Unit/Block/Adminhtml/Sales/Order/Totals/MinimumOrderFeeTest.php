<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 30.03.19
 * Time: 13:15
 */

namespace Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml\Sales\Order\Totals;

use Magento\Sales\Block\Adminhtml\Order\Totals;
use Netzexpert\MinimumOrderFee\Block\Adminhtml\Sales\Order\Totals\MinimumOrderFee;
use Netzexpert\MinimumOrderFee\Test\Unit\Block\Adminhtml\GenericTotalsTest;

class MinimumOrderFeeTest extends GenericTotalsTest
{
    /** @var Totals*/
    protected $parentBlock;

    /** @var MinimumOrderFee */
    protected $model;

    protected function setUp()
    {
        $this->classToTest = MinimumOrderFee::class;
        $this->parentBlockClassName = Totals::class;
        $this->blockCallback = function () {
            return $this->parentBlock;
        };
        parent::setUp();
    }

    protected function getParentBlock($class)
    {
        $block = $this->getTotalsBlock($class)->setLayout($this->layoutMock)->setNameInLayout('order_totals');
        $block->toHtml();
        return $block;
    }
}
