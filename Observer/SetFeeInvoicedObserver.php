<?php
/**
 * Created by PhpStorm.
 * User: styop
 * Date: 13.02.2017
 * Time: 10:47
 */

namespace Netzexpert\MinimumOrderFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SetFeeInvoicedObserver implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * SetFeeInvoicedObserver constructor.
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
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
        $invoice = $observer->getInvoice();
        $invoiceFee = $invoice->getMinimumOrderFee();
        $invoiceBaseFee = $invoice->getBaseMinimumOrderFee();

        $order = $observer->getOrder();
        $order->setMinimumOrderFeeInvoiced($invoiceFee);
        $order->setBaseMinimumOrderFeeInvoiced($invoiceBaseFee);

        return $this;
    }
}