<?php
namespace Ambab\StoreOrderPrefix\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{

    const PREFIX_ENABLED    = 'prefix_section/general/enable';

    const OVERRIDE    = 'prefix_section/general/override';

    const PREFIX    = 'prefix_section/general/order_prefix';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $storeScope;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @param \Magento\Framework\App\Helper\Context                 $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManagerInterface
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        StoreManagerInterface $storeManagerInterface
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $this->configWriter = $configWriter;
        $this->storeManagerInterface = $storeManagerInterface;
        parent::__construct($context);
    }

    /**
     * @return bool Indicate module status
     */
    public function isEnabled($_store_Id)
    {
        return $this->scopeConfig->getValue(self::PREFIX_ENABLED, $this->storeScope, $_store_Id);
    }

    /**
     * @return bool Indicate module status
     */
    public function getPrefixEnabled($_store_Id)
    {
        return $this->scopeConfig->getValue(self::PREFIX_ENABLED, $this->storeScope, $_store_Id);
    }

    /**
     * @return bool
     */
    public function getOverride($_store_Id)
    {
        return $this->scopeConfig->getValue(self::OVERRIDE, $this->storeScope, $_store_Id);
    }

    /**
     * @return String Sales Prefix
     */
    public function getPrefix($_store_Id)
    {
        $prefix = $this->scopeConfig->getValue(self::PREFIX, $this->storeScope, $_store_Id);
        $prefix = trim($prefix);
        if (strlen($prefix) > 32) {
            throw new \Magento\Framework\Exception\LocalizedException("Order Prefix: max 32 characters allowed", 1);
        }
        return $prefix;
    }

    /**
     * @return string current scope
     */
    public function getScope()
    {
        return $this->storeScope;
    }

    /**
     * Update System Configuration on bases on store Id.
     *
     * @return void
     */

    public function updateConfig($path, $value, $storeId = 0)
    {
        $this->configWriter->save($path, $value, $this->storeScope, $storeId);
    }
}
