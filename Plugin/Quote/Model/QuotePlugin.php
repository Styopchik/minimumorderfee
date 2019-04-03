<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 21.08.2017
 * Time: 11:28
 */

namespace Netzexpert\MinimumOrderFee\Plugin\Quote\Model;

use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Quote\Model\Quote;

class QuotePlugin
{
    /** @var ScopeConfigInterface  */
    protected $_scopeConfig;

    /**
     * QuotePlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param Quote $quote
     * @param bool $result
     * @return bool
     */
    public function afterValidateMinimumAmount(
        Quote $quote,
        bool $result
    ){
        $storeId = $quote->getStoreId();
        return $this->_scopeConfig->isSetFlag(
            'sales/minimum_order/allow_checkout',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}