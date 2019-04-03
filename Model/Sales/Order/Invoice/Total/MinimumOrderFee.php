<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 28.08.2017
 * Time: 18:07
 */

namespace Netzexpert\MinimumOrderFee\Model\Sales\Order\Invoice\Total;


class MinimumOrderFee extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @var \Magento\Quote\Model\QuoteValidator|null
     */
    protected $quoteValidator = null;

    /**
     * PaymentMethodFee constructor.
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     */
    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator)
    {
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * Collect grand total address amount
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(
        \Magento\Sales\Model\Order\Invoice $invoice
    ) {
        $order = $invoice->getOrder();
        $fee = $order->getMinimumOrderFee();
        $feeTax = $order->getMinimumOrderFeeTaxAmount();
        $baseFee = $order->getBaseMinimumOrderFee();
        $feeInvoiced = $order->getMinimumOrderFeeInvoiced();
        $baseFeeInvoiced = $order->getBaseMinimumOrderFeeInvoiced();
        $invoice->setData('minimum_order_fee',$fee - $feeInvoiced);
        $invoice->setData('baseminimum_order_fee',$baseFee - $baseFeeInvoiced);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $fee - $feeTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseFee - $feeTax);
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(\Magento\Quote\Model\Quote\Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Quote\Model\Quote\Address\Total $total
    ){
        return [
            'code' => 'Minimum_order_fee',
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