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
     * @param \Ambab\StoreOrderPrefix\Helper\Data                       $prefixConfig
     * @param \Magento\Framework\Message\ManagerInterface               $managerInterface
     * @param \Magento\SalesSequence\Model\Sequence                     $sequence
     * @param \Magento\SalesSequence\Model\ResourceModel\MetaFactory    $metaFactory
     * @param \Magento\SalesSequence\Model\ResourceModel\ProfileFactory $profileFactory
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManagerInterface
     * @param \Magento\Framework\App\Cache\TypeListInterface            $typeListInterface
     * @param \Magento\Framework\App\Request\Http                       $request
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
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            // Config save events identify the selected scope, which is not always a store view.
            $store = $observer->getEvent()->getStore() ?: $this->request->getParam('store');
            $website = $observer->getEvent()->getWebsite() ?: $this->request->getParam('website');
            if ($store) {
                $selectedStore = $this->storeManagerInterface->getStore($store);
                $stores = [$selectedStore];
                $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                $scopeId = (int) $selectedStore->getId();
            } elseif ($website) {
                $selectedWebsite = $this->storeManagerInterface->getWebsite($website);
                $stores = $selectedWebsite->getStores();
                $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
                $scopeId = (int) $selectedWebsite->getId();
            } else {
                $stores = $this->storeManagerInterface->getStores();
                $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
                $scopeId = 0;
            }
            $updates = [];
            foreach ($stores as $selectedStore) {
                $storeId = (int) $selectedStore->getId();
                if (!$storeId || !$this->prefixConfig->isEnabled($storeId)
                    || !$this->prefixConfig->getOverride($storeId)) {
                    continue;
                }
                $prefix = $this->prefixConfig->getPrefix($storeId);
                if ($prefix === '') {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Enter an order prefix before overriding the sequence.'));
                }
                $metaIds = $this->loadMetaByStore($storeId);
                if (!$metaIds) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('No sales sequence exists for store %1.', $storeId));
                }
                $updates[] = [$metaIds, $prefix];
            }
            // Validate every target before changing any sequence.
            foreach ($updates as [$metaIds, $prefix]) {
                $this->updateProfileByMeta($metaIds, $prefix);
            }
            if ($updates) {
                $this->prefixConfig->updateConfig(self::OVERRIDE, 0, $scopeId, $scope);
                $this->typeListInterface->cleanType('config');
                $this->managerInterface->addSuccessMessage(__('Prefix Updated into the Sequence'));
            }
        } catch (\Exception $error) {
            $this->managerInterface->addErrorMessage(__($error->getMessage()));
        }
    }

    /**
     * Fetch MetaIds of sales sequence module for current store, to update prefix on sales profile
     * table.
     *
     * @return []
     */

    public function loadMetaByStore($storeId = null)
    {
        $_store_Id = $storeId ?? (int) $this->request->getParam('store');
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
     * Update profile prefix by metaids
     *
     * @param  []     $metaIds metaIds to update profile prefix
     * @param  String $prefix  sales sequence prefix to be updated.
     * @return void
     */
    public function updateProfileByMeta($metaIds, $prefix)
    {
        
        $profile = $this->profileFactory->create();
        $profile->getConnection()->update(
            $profile->getConnection()->getTableName($profile->getMainTable()),
            ["prefix" => $prefix],
            ['meta_id IN (?)' => $metaIds, 'is_active = ?' => 1]
        );
    }
}
