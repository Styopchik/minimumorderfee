<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 18.04.19
 * Time: 16:12
 */

namespace Netzexpert\MinimumOrderFee\Plugin;

use Magento\CheckoutAgreements\Api\CheckoutAgreementsListInterface;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;
use Netzexpert\MinimumOrderFee\Plugin\CheckoutAgreements\Model\AgreementsProviderPlugin;

class CheckoutAgreementsListPlugin extends AgreementsProviderPlugin
{
    /**
     * @param CheckoutAgreementsListInterface $agreementsList
     * @param AgreementInterface[] $agreements
     * @return mixed
     */
    public function afterGetList(
        CheckoutAgreementsListInterface $agreementsList,
        $agreements
    ) {
        $fee = $this->getMinimumOrderFeeValue();
        if (!$fee) {
            foreach ($agreements as $key => $agreement) {
                if ($agreement->getName() == 'Minimum Order Fee') {
                    unset($agreements[$key]);
                }
            }
        }
        return $agreements;
    }
}
