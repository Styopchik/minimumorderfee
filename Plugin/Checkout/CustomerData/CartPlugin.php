<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 03.04.19
 * Time: 13:31
 */

namespace Netzexpert\MinimumOrderFee\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\Cart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CartPlugin
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig  = $scopeConfig;
    }

    public function afterGetSectionData(Cart $cart, array $result)
    {
        $enabled = $this->scopeConfig->getValue(
            'sales/minimum_order/active',
            ScopeInterface::SCOPE_STORE
        ) && $this->scopeConfig->getValue(
                'sales/minimum_order/allow_checkout',
                ScopeInterface::SCOPE_STORE
            );
        $amount = $this->scopeConfig->getValue(
            'sales/minimum_order/amount',
            ScopeInterface::SCOPE_STORE
        );
        if ($enabled && $result['subtotalAmount'] < $amount) {
            $result['minimumOrderFeeMessage'] = $this->scopeConfig->getValue(
                'sales/minimum_order/description',
                ScopeInterface::SCOPE_STORE
            );

        }
        return $result;
    }
}
