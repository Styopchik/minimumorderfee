<?php

namespace NetzExpert\MinimumOrderFee\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /** @var \Magento\CheckoutAgreements\Model\AgreementFactory */
    private $_agreementFactory;

    /** @var \Magento\CheckoutAgreements\Model\CheckoutAgreementsRepository */
    private $_agreementsRepository;

    /** @var \Magento\Cms\Model\BlockFactory */
    private $_blockFactory;

    /** @var \Magento\Cms\Model\BlockRepository  */
    private $_blockRepository;

    public function __construct(
        \Magento\CheckoutAgreements\Model\AgreementFactory $agreementFactory,
        \Magento\CheckoutAgreements\Model\CheckoutAgreementsRepository $agreementsRepository,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository
    ){
        $this->_agreementFactory     = $agreementFactory;
        $this->_agreementsRepository = $agreementsRepository;
        $this->_blockFactory = $blockFactory;
        $this->_blockRepository = $blockRepository;
    }

    /**
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\CheckoutAgreements\Model\Agreement $agreement */
        $agreement = $this->_agreementFactory->create();
        $agreement = $agreement->setStoreId(0)->load('Minimum Order Fee', 'name');
        if (!$agreement->getId()) {
            $agreement = $this->_agreementFactory->create();
        }
        $agreement->setName('Minimum Order Fee')
            ->setIsActive(1)
            ->setIsHtml(1)
            ->setMode(1)
            ->setCheckboxText('I hereby confirm that I have taken note of the surcharge')
            ->setContent('{{block class="Magento\Cms\Block\Block" block_id="minimum_order_fee"}}')
            ->setStores([0]);

        $this->_agreementsRepository->save($agreement);

        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->_blockFactory->create();
        $block = $block->load('minimum_order_fee', 'identifier');
        $block->setIsActive(1)
            ->setTitle('Minimum Order Fee')
            ->setIdentifier('minimum_order_fee')
            ->setContent('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mihi enim satis est, ipsis non satis. At multis se probavit. Quare conare, quaeso. Cur id non ita fit? Minime vero istorum quidem, inquit. Age sane, inquam. </p>
                            <p>Quis Aristidem non mortuum diligit? Quae contraria sunt his, malane? Omnia peccata paria dicitis. At hoc in eo M. </p>
                            <p>Duo Reges: constructio interrete. Audeo dicere, inquit. Tenent mordicus. Quis Aristidem non mortuum diligit? Quis Aristidem non mortuum diligit? In schola desinis. </p>')
            ->setStores([0]);
        $this->_blockRepository->save($block);

    }
}