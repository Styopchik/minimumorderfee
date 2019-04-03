<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 28.08.2017
 * Time: 17:41
 */

namespace Netzexpert\MinimumOrderFee\Block\Adminhtml\Sales\Order\Invoice\Totals;

use Magento\Framework\DataObject;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Totals;

class MinimumOrderFee extends Totals
{
    /**
     * Initialize totals object
     *
     * @return $this
     */
    public function initTotals()
    {
        if (!(float)$this->getSource()->getData('minimum_order_fee')) {
            return $this;
        }
        $total = new DataObject(
            [
                'code' => $this->getNameInLayout(),
                'strong' => false,
                'value' => $this->getSource()->getData('minimum_order_fee'),
                'label' => __('Minimum Order Fee')
            ]
        );
        /** @var Totals $parentBlock */
        $parentBlock = $this->getParentBlock();
        $parentBlock->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
