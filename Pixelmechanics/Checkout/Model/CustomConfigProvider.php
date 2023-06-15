<?php
/**
 * @author: KL
 * @description: to get the dynamic value of Tax from the admin using data helper to display on the checkout inkl. MwSt OR zzgl. Mswt
 */
namespace Pixelmechanics\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Pixelmechanics\Checkout\Helper\Data;

class CustomConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    protected $_helperData;

    public function __construct(LayoutInterface $layout, Data $helperData)
    {
        $this->_layout = $layout;
        $this->_helperData = $helperData;
    }

    /** Return the vat information vatExcluded/vatIncluded */
    public function getConfig()
    {
        $vatInfo = [];
        $vatInfo = [
            'vatPriceInformation' => $this->_helperData->getVatInfoPriceText(),
            'vatSubtotalInformation' => $this->_helperData->getVatInfoSubtotalText()
        ];
        return $vatInfo;
    }
}
