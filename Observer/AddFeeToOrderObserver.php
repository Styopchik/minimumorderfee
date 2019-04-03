<?php
/**
 * Created by PhpStorm.
 * User: styop
 * Date: 08.02.2017
 * Time: 15:59
 */

namespace Netzexpert\MinimumOrderFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * AddFeeToOrderObserver constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_logger = $logger;
    }

    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $feeAmount = $quote->getShippingAddress()->getData('minimum_order_fee');
        $baseFeeAmount = $quote->getShippingAddress()->getData('base_minimum_order_fee');
        if(!$feeAmount || !$baseFeeAmount) {
            return $this;
        }
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('minimum_order_fee', $feeAmount);
        $order->setData('base_minimum_order_fee', $baseFeeAmount);
        $order->setData('minimum_order_fee_tax_amount', $quote->getShippingAddress()->getData('minimum_order_fee_tax_amount'));

        return $this;
    }
}