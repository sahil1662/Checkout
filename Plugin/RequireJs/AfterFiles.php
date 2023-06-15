<?php

namespace Pixelmechanics\Checkout\Plugin\RequireJs;

class AfterFiles
{
    protected $scopeConfig;
    const XML_PATH_EMAIL_RECIPIENT = 'checkout_section/general/enable';

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    public function afterGetFiles(
        \Magento\Framework\RequireJs\Config\File\Collector\Aggregated $subject,
        $result
    ){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $status = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);

        if(!$status) {
            foreach ($result as $key => &$file) {
                if ($file->getModule() == "Pixelmechanics_Checkout") {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }
}