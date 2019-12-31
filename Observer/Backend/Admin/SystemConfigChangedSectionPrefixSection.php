<?php


namespace Ambab\StoreOrderPrefix\Observer\Backend\Admin;

use Ambab\StoreOrderPrefix\Helper\Data as prefixHelper;
use Magento\Framework\Message\ManagerInterface;
use Magento\SalesSequence\Model\Sequence;
use Magento\SalesSequence\Model\ResourceModel\MetaFactory;
use Magento\SalesSequence\Model\ResourceModel\ProfileFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class SystemConfigChangedSectionPrefixSection implements \Magento\Framework\Event\ObserverInterface
{
    const OVERRIDE    = 'prefix_section/general/override';

    /**
     * @var \Ambab\StoreOrderPrefix\Helper\Data
     */
    protected $prefixConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $managerInterface;

    /**
     * @var \Magento\SalesSequence\Model\Sequence
     */
    protected $sequence;

    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\Meta
     */
    protected $metaFactory;

    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\Profile
     */
    protected $profileFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeListInterface;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Constructor
     *
     * @param \Ambab\StoreOrderPrefix\Helper\Data $prefixConfig
     * @param \Magento\Framework\Message\ManagerInterface $managerInterface
     * @param \Magento\SalesSequence\Model\Sequence $sequence
     * @param \Magento\SalesSequence\Model\ResourceModel\MetaFactory $metaFactory
     * @param \Magento\SalesSequence\Model\ResourceModel\ProfileFactory $profileFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeListInterface
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        prefixHelper $prefixConfig,
        ManagerInterface $managerInterface,
        Sequence $sequence,
        MetaFactory $metaFactory,
        ProfileFactory $profileFactory,
        StoreManagerInterface $storeManagerInterface,
        TypeListInterface $typeListInterface,
        \Magento\Framework\App\Request\Http $requests
    ) {
        $this->prefixConfig = $prefixConfig;
        $this->managerInterface = $managerInterface;
        $this->sequence = $sequence;
        $this->metaFactory = $metaFactory;
        $this->profileFactory = $profileFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->typeListInterface = $typeListInterface;
        $this->request = $requests;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $_store_Id = (int) $this->request->getParam('store');
        if ($this->prefixConfig->isEnabled($_store_Id)) {
            if ($this->prefixConfig->getOverride($_store_Id)) {
                try {
                    $_metaInfo = $this->metaFactory->create();
                    $_metaIds = $this->loadMetaByStore();
                    $_prefix = $this->prefixConfig->getPrefix($_store_Id);
                    $this->updateProfileByMeta($_metaIds, $_prefix);

                    $this->prefixConfig->updateConfig(self::OVERRIDE, 0, $_store_Id);
                    
                    $this->typeListInterface->cleanType("config");

                    $this->managerInterface->addSuccess(__("Prefix Updated into the Sequence"));
                } catch (\Exception $e) {
                    $this->managerInterface->addError(__($e->getMessage()));
                }

            } else {
                $this->managerInterface->addError(__("Please allow to override current prefix."));
            }
        } else {
            $this->managerInterface->addError(__("Please Enabled Prefix Module."));
        }
    }

    /**
     *
     * Fetch MetaIds of sales sequence module for current store, to update prefix on sales profile
     * table.
     *
     * @return []
     */

    public function loadMetaByStore()
    {
        $_store_Id = (int) $this->request->getParam('store');
        $meta = $this->metaFactory->create();
        $connection = $meta->getConnection();
        $bind = ['store_id' => $_store_Id];
        $select = $connection->select()->from(
            $meta->getMainTable(),
            ['meta_id']
        )->where(
            'store_id = :store_id'
        );
        $metaIds = $connection->fetchCol($select, $bind);

        return $metaIds;
    }

    /**
     *
     * Update profile prefix by metaids
     *
     * @param [] $metaIds metaIds to update profile prefix
     * @param String $prefix sales sequence prefix to be updated.
     * @return void
     */
    public function updateProfileByMeta($metaIds, $prefix)
    {
        
        $profile = $this->profileFactory->create();
        $profile->getConnection()->update(
            $profile->getConnection()->getTableName($profile->getMainTable()),
            ["prefix" => $prefix],
            ['meta_id IN (?)' => $metaIds]
        );
    }
}
