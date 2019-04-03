<?php
/**
 * Created by PhpStorm.
 * User: Andrew Stepanchuk
 * Date: 29.08.2017
 * Time: 19:58
 */

namespace Netzexpert\MinimumOrderFee\Plugin;


class CheckoutAgreementsRepositoryPlugin
{
    /** @var \Magento\Checkout\Model\Session  */
    protected $_checkoutSession;

    /**
     * CheckoutAgreementsRepositoryPlugin constructor.
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session
    ){
        $this->_checkoutSession = $session;
    }

    /**
     * @param \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface $agreementsRepository
     * @param \Magento\CheckoutAgreements\Api\Data\AgreementInterface[] $result
     * @return \Magento\CheckoutAgreements\Api\Data\AgreementInterface[] Array of checkout agreement data objects.
     */
    public function afterGetList(
        \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface $agreementsRepository,
        $result
    ){
        $fee = $this->_getMinimumOrderFeeValue();
        if(!$fee){
            foreach ($result as $key => $agreement){
                if ($agreement->getName() == 'Minimum Order Fee') {
                    unset ($result[$key]);
                }
            }
        }
        return $result;
    }

    protected function _getMinimumOrderFeeValue(){
        $totals = $this->_checkoutSession->getQuote()->getTotals();
        if(!isset($totals['minimumorderfee'])){
            return false;
        }
        /** @var \Magento\Quote\Model\Quote\Address\Total $minimumOrderFee */
        $minimumOrderFee = $totals['minimumorderfee'];
        return $minimumOrderFee->getValue();
    }
}