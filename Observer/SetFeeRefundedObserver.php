<?php
/**
 * Created by PhpStorm.
 * User: styop
 * Date: 13.02.2017
 * Time: 11:22
 */

namespace Netzexpert\MinimumOrderFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SetFeeRefundedObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * SetFeeRefundedObserver constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_request = $request;
        $this->_logger = $logger;
    }

    /**
     * Set payment fee invoiced to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditMemo = $observer->getCreditmemo();
        $creditMemoFee = $creditMemo->getMinimumOrderFee();
        $creditMemoBaseFee = $creditMemo->getBaseMinimumOrderFee();

        $order = $observer->getCreditmemo()->getOrder();
        $order->setMinimumOrderFeeRefunded($creditMemoFee);
        $order->setBaseMinimumOrderFeeRefunded($creditMemoBaseFee);

        return $this;
    }
}