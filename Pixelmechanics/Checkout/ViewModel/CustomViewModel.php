<?php
namespace Pixelmechanics\Checkout\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomViewModel implements ArgumentInterface
{
    protected $scopeConfig;

    const XML_PATH_EMAIL_RECIPIENT = 'checkout_section/general/enable';

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    public function checkCondition()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);

    }
}