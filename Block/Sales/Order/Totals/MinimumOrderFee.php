<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 29.08.2017
 * Time: 17:40
 */

namespace Netzexpert\MinimumOrderFee\Block\Sales\Order\Totals;


class MinimumOrderFee extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    }
    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order;
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        if(!(float)$this->_source->getMinimumOrderFee()) {
            return $this;
        }
        $fee = new \Magento\Framework\DataObject(
            [
                'code' => 'minimum_order_fee',
                'strong' => false,
                'value' => $this->_source->getMinimumOrderFee(),
                'label' => __('Minimum Order Fee'),
            ]
        );

        $parent->addTotal($fee, 'minimum_order_fee');
        return $this;
    }
}