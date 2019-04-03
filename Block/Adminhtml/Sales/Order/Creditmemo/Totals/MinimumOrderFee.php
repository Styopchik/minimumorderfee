<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 28.08.2017
 * Time: 18:41
 */

namespace Netzexpert\MinimumOrderFee\Block\Adminhtml\Sales\Order\Creditmemo\Totals;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Tax\Model\Config as TaxConfig;

class MinimumOrderFee extends Template
{
    /**
     * Source object
     *
     * @var DataObject
     */
    protected $_source;

    /**
     * Tax config
     *
     * @var TaxConfig
     */
    protected $_taxConfig;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Context $context
     * @param TaxConfig $taxConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        TaxConfig $taxConfig,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_taxConfig = $taxConfig;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Initialize creditmemo agjustment totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getSource();

        /** @var Totals $parent */
        $parent = $this->getParentBlock();
        /** @var Creditmemo _source */
        $this->_source = $parent->getSource();
        if (!(float)$parent->getSource()->getData('minimum_order_fee')) {
            return $this;
        }
        $total = new DataObject([
            'code'          => 'minimum_order_fee',
            'block_name'    => $this->getNameInLayout()
        ]);

        $parent->addTotalBefore($total, 'tax');
        return $this;
    }

    /**
     * Get source object
     *
     * @return Creditmemo
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get credit memo payment method fee amount depend on configuration settings
     *
     * @return float
     */
    public function getFeeAmount()
    {
        //Todo calculate tax
        $source = $this->getSource();
        if ($source->getId()) {
            $minimumOrderFee = $source->getData('minimum_order_fee');
        } else {
            $minimumOrderFee = $source->getOrder()->getData('minimum_order_fee') - $source->getOrder()->getData('payment_method_fee_refunded');
        }
        return $this->priceCurrency->round($minimumOrderFee) * 1;
    }

    /**
     * Get label for payment method fee based on configuration settings
     *
     * @return string
     */
    public function getFeeLabel()
    {
        $label = __('Minimum Order Fee');
        return $label;
    }

    /**
     * Retrieve formatted price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getSource()->getOrder()->formatPrice($price);
    }
}