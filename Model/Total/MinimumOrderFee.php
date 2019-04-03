<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 21.08.2017
 * Time: 11:59
 */

namespace Netzexpert\MinimumOrderFee\Model\Total;

use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Quote\Model\Quote\Address;

class MinimumOrderFee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /** @var ScopeConfigInterface  */
    protected $_scopeConfig;

    /**
     * @var \Magento\Quote\Model\QuoteValidator|null
     */
    protected $quoteValidator = null;

    /**
     * @var \Magento\Customer\Model\ResourceModel\GroupRepository
     */
    protected $_customerGroupRepository;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * MinimumOrderFee constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Customer\Model\ResourceModel\GroupRepository $groupRepository
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Customer\Model\ResourceModel\GroupRepository $groupRepository,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->quoteValidator = $quoteValidator;
        $this->_customerGroupRepository = $groupRepository;
        $this->_taxCalculation = $calculation;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);


        if (!count($shippingAssignment->getItems()) || !$this->_isActive($quote)) {
            return $this;
        }

        $groupId = $quote->getCustomerGroupId();
        $group = $this->_customerGroupRepository->getById($groupId);
        $customerTaxClassId = $group->getTaxClassId();
        $store = $this->_storeManager->getStore();
        $request = $this->_taxCalculation->getRateRequest($quote->getShippingAddress(), $quote->getBillingAddress(), $customerTaxClassId, $store);
        $request->setData('product_class_id', 1); //Todo get/set fee tax class id

        $taxPercent = $this->_taxCalculation->getRate($request);

        $fee = $this->_scopeConfig->getValue('sales/minimum_order/fee',ScopeInterface::SCOPE_STORE, $quote->getStoreId());

        $tax = $this->_taxCalculation->calcTaxAmount($fee, $taxPercent, $priceIncludeTax = true, $round = false);

        /*$feeExclTax = ($fee / (100 + $taxPercent) * 100);*/
        $feeExclTax = $fee - $tax;


        if ($this->_isApplicable($quote)) {
            $total->setTotalAmount('minimumorderfee', $feeExclTax);
            $total->setBaseTotalAmount('minimumorderfee', $feeExclTax);

            $total->setMinimumOrderFee($fee);
            $total->setBaseMinimumOrderFee($fee);

            $total->setMinimumOrderFeeTaxAmount($tax);
            $total->setBaseMinimumOrderFeeTaxAmount($tax);

            $taxable = [
                'code' => 'minimumorderfee',
                'type' => 'fee',
                'quantity' => 1,
                'tax_class_id' => 1,
                'unit_price' => $feeExclTax,
                'base_unit_price' => $feeExclTax,
                'price_includes_tax' => false,
                'associated_item_code' => ''
            ];

            $shippingAddress = $quote->getShippingAddress();
            $associatedTaxables = $shippingAddress->getAssociatedTaxables();
            if(!is_array($associatedTaxables)){
                $associatedTaxables = [];
            }
            array_push($associatedTaxables, $taxable);
            $shippingAddress->setAssociatedTaxables($associatedTaxables);
        } else {
            $total->setTotalAmount('minimumorderfee', 0);
            $total->setBaseTotalAmount('minimumorderfee', 0);

            $total->setMinimumOrderFee(0);
            $total->setBaseMinimumOrderFee(0);

            $total->setMinimumOrderFeeTaxAmount(0);
            $total->setBaseMinimumOrderFeeTaxAmount(0);
        }


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
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $value = ($this->_isApplicable($quote)) ?
            $fee = $this->_scopeConfig->getValue('sales/minimum_order/fee',ScopeInterface::SCOPE_STORE, $quote->getStoreId()) :
            0;
        return [
            'code' => 'minimumorderfee',
            'title' => __('Minimum order fee'),
            'value' => $value
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Minimum order fee');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    private function _isActive($quote){
        $storeId = $quote->getStoreId();
        return $this->_scopeConfig->isSetFlag('sales/minimum_order/active',ScopeInterface::SCOPE_STORE, $storeId)
            && $this->_scopeConfig->isSetFlag('sales/minimum_order/allow_checkout',ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    private function _isApplicable($quote){
        $addresses = $quote->getAllAddresses();
        $multishipping = $quote->getIsMultishipping();
        $storeId = $quote->getStoreId();
        $minOrderMulti = $this->_scopeConfig->isSetFlag(
            'sales/minimum_order/multi_address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $taxInclude = $this->_scopeConfig->getValue(
            'sales/minimum_order/tax_including',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $minAmount = $this->_scopeConfig->getValue(
            'sales/minimum_order/amount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$multishipping) {
            foreach ($addresses as $address) {
                /* @var $address Address */
                if (!$address->validateMinimumAmount()) {
                    return true;
                }
            }
            return false;
        }

        if (!$minOrderMulti) {
            foreach ($addresses as $address) {
                $taxes = ($taxInclude) ? $address->getBaseTaxAmount() : 0;
                foreach ($address->getQuote()->getItemsCollection() as $item) {
                    /** @var \Magento\Quote\Model\Quote\Item $item */
                    $amount = $item->getBaseRowTotal() - $item->getBaseDiscountAmount() + $taxes;
                    if ($amount < $minAmount) {
                        return true;
                    }
                }
            }
        } else {
            $baseTotal = 0;
            foreach ($addresses as $address) {
                $taxes = ($taxInclude) ? $address->getBaseTaxAmount() : 0;
                $baseTotal += $address->getBaseSubtotalWithDiscount() + $taxes;
            }
            if ($baseTotal < $minAmount) {
                return true;
            }
        }
        return false;
    }
}