<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 29.08.2017
 * Time: 18:12
 */

namespace Netzexpert\MinimumOrderFee\Block\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;

class MinimumOrderFee extends Template
{
    /**  @var Order */
    private $_order;

    /** @var DataObject */
    private $_source;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function displayFullSummary()
    {
        return true;
    }

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        $title = __('Minimum Order Fee');
        $store = $this->getStore();
        if($this->_order->getMinimumOrderFee()!=0){
            $feeTotal = new \Magento\Framework\DataObject(
                [
                    'code' => 'minimum_order_fee',
                    'strong' => false,
                    'value' => $this->_order->getMinimumOrderFee(),
                    'label' => __($title),
                ]
            );
            $parent->addTotalBefore($feeTotal, 'tax');
        }
        return $this;
    }
    /**
     * Get order store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_order->getStore();
    }
    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }
    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }
    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}