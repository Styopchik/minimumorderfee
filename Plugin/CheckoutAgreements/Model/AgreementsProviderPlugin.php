<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 22.02.18
 * Time: 15:08
 */

namespace Netzexpert\MinimumOrderFee\Plugin\CheckoutAgreements\Model;


class AgreementsProviderPlugin
{

    /** @var \Magento\Checkout\Model\Session  */
    protected $checkoutSession;

    /** @var \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface  */
    protected $chekoutAgreementsRepository;

    /**
     * AgreementsProviderPlugin constructor.
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository
    ){
        $this->checkoutSession = $session;
        $this->chekoutAgreementsRepository = $checkoutAgreementsRepository;
    }


    /**
     * @param \Magento\CheckoutAgreements\Model\AgreementsProvider $agreementsProvider
     * @param int[] $agreementIds
     */
    public function afterGetRequiredAgreementIds(
        \Magento\CheckoutAgreements\Model\AgreementsProvider $agreementsProvider,
        $agreementIds
    ) {
        $fee = $this->getMinimumOrderFeeValue();
        if(!$fee){
            foreach ($agreementIds as $key => $agreementId){
                $agreement = $this->chekoutAgreementsRepository->get($agreementId);
                if ($agreement->getName() == 'Minimum Order Fee') {
                    unset ($agreementIds[$key]);
                }
            }
        }
        return $agreementIds;
    }

    /**
     * @return bool
     */
    protected function getMinimumOrderFeeValue(){
        $totals = $this->checkoutSession->getQuote()->getTotals();
        if(!isset($totals['minimumorderfee'])){
            return false;
        }
        /** @var \Magento\Quote\Model\Quote\Address\Total $minimumOrderFee */
        $minimumOrderFee = $totals['minimumorderfee'];
        return $minimumOrderFee->getValue();
    }
}