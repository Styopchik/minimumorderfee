<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 29.08.2017
 * Time: 22:18
 */

namespace Netzexpert\MinimumOrderFee\Model\Checkout;

use \Magento\Checkout\Model\ConfigProviderInterface;
use \Magento\Store\Model\ScopeInterface;

class MinimumOrderFeeConfigProvider implements ConfigProviderInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $_scopeConfig;

    /** @var \Magento\Framework\Pricing\Helper\Data  */
    protected $_currencyHelper;

    /**
     * MinimumOrderFeeConfigProvider constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\Helper\Data $currencyHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->_currencyHelper = $currencyHelper;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $minimumOrderAmount = $this->_scopeConfig->getValue('sales/minimum_order/amount',ScopeInterface::SCOPE_STORE);
        return [
            'minimumOrderAmount' => $this->_currencyHelper->currency($minimumOrderAmount, true, false)
        ];
    }

}