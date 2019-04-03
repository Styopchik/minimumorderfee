<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 28.08.2017
 * Time: 18:33
 */

namespace Netzexpert\MinimumOrderFee\Model\Sales\Order\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class MinimumOrderFee extends AbstractTotal
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * PaymentMethodFee constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->_request = $request;
    }

    /**
     * Collect grand total address amount
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(
        \Magento\Sales\Model\Order\Creditmemo $creditmemo
    ) {
        $order = $creditmemo->getOrder();
        // amounts
        $orderFeeAmount = $order->getMinimumOrderFee();
        $orderBaseFeeAmount = $order->getBaseMinimumOrderFee();
        $allowedAmount = $orderFeeAmount - $order->getMinimumOrderFeeRefunded();
        $baseAllowedAmount = $orderBaseFeeAmount - $order->getBaseMinimumOrderFeeRefunded();
        $allowedTaxAmount = $order->getMinimumOrderFeeTaxAmount();

        $creditmemo->setData('minimum_order_fee',$allowedAmount);
        $creditmemo->setData('base_minimum_order_fee',$baseAllowedAmount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $allowedAmount - $allowedTaxAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $allowedAmount - $allowedTaxAmount);
        return $this;
    }


    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        \Magento\Quote\Model\Quote\Address\Total $total
    ){
        return [
            'code' => 'minimum_order_fee',
            'title' => 'Minimum Order Fee',
            'value' => $total->getMinimumOrderFee()
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Minimum Order Fee');
    }
}