<?php

namespace Pixelmechanics\Checkout\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Store\Model\StoreManagerInterface;
     */

    protected $_storeManager;
    protected $_registry;

    /**
     * Catalog product link
     *
     * @var \Magento\GroupedProduct\Model\ResourceModel\Product\Link
     */
    protected $productLinks;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $_priceHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /*
     * @var  \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /** @var \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet **/
    protected $attributeSet;

    /**
     * @var  \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    protected $_productModel;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\Helper\Data $pricehelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\App\Request\Http $request
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\GroupedProduct\Model\ResourceModel\Product\Link $catalogProductLink,
        product $_productModel,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
    ) {
        $this->_storeManager =  $storeManager;
        $this->_filterProvider =$filterProvider;
        $this->_registry = $registry;
        $this->_productModel = $_productModel;
        $this->productLinks = $catalogProductLink;
        $this->attributeSet = $attributeSet;
        $this->_priceHelper  = $pricehelper;
        $this->_httpContext = $httpContext;
        $this->_request = $request;
        $this->_scopeConfig = $scopeConfig;
        $this->_wishlist = $wishlist;
        parent::__construct($context);
    }

    /*
    * @author : PS
    * @Date : 24th April'19
    * @Description : Check if the customer logged in.
    * @return Bool
    */
    public function isLoggedIn()
    {
        $isLoggedIn = $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isLoggedIn;
    }

    /*
    * @author : PS
    * @Date : 24th April'19
    * @Description : Get the value from the store configuration of the current store following the object parameters
    * @return String
    */
    public function getStoreConfig($object)
    {

        $loadConfiguration = $this->_scopeConfig->getValue($object, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $loadConfiguration;
    }


    /**
     * get the store base Url
     * @return string
     */
    public function getStoreBaseUrl()
    {

        $storeBaseUrl =  $this->_storeManager->getStore()->getBaseUrl();
        return $storeBaseUrl;
    }

    /**
     * get the current product
     * @return string
     */
    public function getCurrentProduct()
    {

        return $this->_registry->registry('current_product');
    }


    /**
     * Get the Store Code
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * Get meta tag code from the system admin configuration to scan the security
     * @author: NA
     * @date: 17.01.2020
     * @link:https://trello.com/c/LZeRJFfa/32-magento-security-scan-widgetcode-im-header
     * @return string
     */


    public function getMetaTag($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        /** Check the meta tag module status Enable/Disable */
        $moduelStatus = $this->scopeConfig->getValue('pixelmechanics/meta_tag/meta_tag_status', $scope);

        if ($moduelStatus == 1) { //check condition if the module is enable

            $metacode = $this->scopeConfig->getValue('pixelmechanics/meta_tag/meta_tag_code', $scope); //get the meta tag code value from the admin configuration
            return $metacode;
        }
    }

    /**
     * Get prodcut tax class code from the system admin configuration
     * @author: YK
     * @date: 31.01.2020
     * @link:https://trello.com/c/YiIlFx1J/35-product-detail-page
     * @return string
     */

    public function getProductTaxclass($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $moduelStatus = $this->scopeConfig->getValue('pixelmechanics/meta_tag/product_tax_class', $scope);
        return $moduelStatus;
    }

    /**
     * get string (attribute value)
     * @author: N.A 12.03.2020
     * @link: https://trello.com/c/FJBmxh7e/37-checkout
     * @return string
     */

    public function getWysiwygAttributeValue($attribute)
    {
        $attributeValue = $this->_filterProvider->getPageFilter()->filter(
            $attribute
        );
        return $attributeValue;
    }

    /**
     * @author: N.A 30.04.2020
     * @link: https://trello.com/c/gswCiUpO/81-grundpreis-base-price-price-per-1-unit
     * @description: to calculate the base price per Unit
     */

    /** Return the Per Unit(kg, container, etc) price, we are using on the slider  */
    // public function getPerUnitPrice($_product)
    // {
    //     $baseUnit   = $_product->getAttributeText('base_unit'); // get the base Unit kg, container etc
    //     $unit       = $_product->getAttributeText('base_unit_value');//get the base unit value like 1, 5, 100 etc
    //     if($baseUnit && $unit){

    //         $priceWithoutCurrentySymbol  = $_product->getFinalPrice(); //get the final price of product
    //         $pricePerKg = $priceWithoutCurrentySymbol / max($unit,1);  // calculate the the price per unit
    //         $pricePerKg = $this->_priceHelper->currency($pricePerKg, true, false);
    //         $pricePerUnit    = '';
    //         $pricePerUnit   .= '<span class="product-per-unit-price">';
    //         $pricePerUnit   .= '( ';
    //         $pricePerUnit   .= $pricePerKg;
    //         $pricePerUnit   .= ' / ';
    //         $pricePerUnit   .= '1 ';
    //         $pricePerUnit   .= $baseUnit;
    //         $pricePerUnit   .= ' )';

    //         return $pricePerUnit;
    //     }
    // }

    /** Get the Product Unit Value like 5 Gebinde of base price*/
    // public function getProductUnit($_product)
    // {

    //     $baseUnit1   = $_product->getAttributeText('base_unit'); // get the base Unit kg, container etc
    //     $unit1 = $_product->getAttributeText('base_unit_value'); //get the base unit value like 1, 5, 100 etc

    //     if($unit1 && $baseUnit1)
    //     {
    //         $productUnit     = '';
    //         $productUnit    .= '<span class="product-unit">';
    //         $productUnit    .= ' / ';
    //         $productUnit    .=  $unit1;
    //         $productUnit    .= '&nbsp;';
    //         $productUnit    .= $baseUnit1;
    //         $productUnit    .= '</span>';
    //         return $productUnit;
    //     }

    // }

    /** return the product base unit like kg, container etc*/
    // public function getBaseUnit($_product){

    //     return $_product->getAttributeText('base_unit');

    // }

    /** Return the product unit value like 5,10,100 etc*/
    // public function getBaseUnitValue($_product){

    //     return $unitValue = $_product->getAttributeText('base_unit_value');

    // }


    /*
    *function for creating VAT info in cart and checkout table header
    *PM MD 2020-06-22 see Requirements https://trello.com/c/vc14GwHF/26-magento-shops-mwst-mehrwertsteuer-tax-shipping-infos#comment-5eecb5ca9e3b6629950c6ced
    */
    public function getVatInfoText()
    {

        /*Get Vat backend setting
        * 1: excluding tax
        * 2. incliding tax
        * 3. Including/excluding tax both*/
        $vatSetting= $this->getCartVatInfo();

        //Check which Backendsettings are used and set right language key
        $vatInfoText="";
        if ($vatSetting == 1) {
            $vatInfoText = __('vatExcluded');
        }
        if ($vatSetting == 2) {
            $vatInfoText = __('vatIncluded');
        }

        //create html output string
        $return = "<p class='vat_info'>$vatInfoText</p>";

        //return html output
        return $return;
    }

    /**
     * get Pub media Url of website
     * @return string
     * PM N.A on 09.06.2020 @link:https://trello.com/c/jGh1bUvj/92-header-with-sticky-topbar-and-language-switcher-card-search-logo-create-account-and-login
     * @description: get the media url to add the flag image in language dropdown
     */

    public function getPubMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Return current Page Action Name
     * Updated By PS on 11.06.2020
     * https://trello.com/c/jGh1bUvj/92-header-with-sticky-topbar-and-language-switcher-card-search-logo-create-account-and-login
     */
    public function getCurrentPage()
    {
        return $this->_request->getFullActionName();
    }
    /**
     * function for Catalog VAT info on PDP Page 2020 June 22 PV
     */
    public function getCatalogVatInfo()
    {
        /**
         *https://trello.com/c/vc14GwHF/26-magento-shops-mwst-mehrwertsteuer-tax-shipping-infos
         * 1: excluding tax
         * 2. incliding tax
         * 3. Including/excluding tax both
         */
        //get current storeID
        $storeID = $this->_storeManager->getStore()->getStoreId();
        return $this->scopeConfig->getValue('tax/display/type', ScopeInterface::SCOPE_STORE);
    }

    /**
     * function for Cart VAT info on PDP Page 2020 June 23 PV
     */
    public function getCartVatInfo()
    {
        /**
         *https://trello.com/c/vc14GwHF/26-magento-shops-mwst-mehrwertsteuer-tax-shipping-infos
         * 1: excluding tax
         * 2. incliding tax
         * 3. Including/excluding tax both
         */
        //get current storeID
        $storeID = $this->_storeManager->getStore()->getStoreId();
        return $this->scopeConfig->getValue('tax/cart_display/price', ScopeInterface::SCOPE_STORE);
    }


    /**
     * Get customer ID from the session
     * @param int $customerId
     */
    public function getCustomerIdFromSession()
    {
        $customerId = $this->_httpContext->getValue('customer_id');
        return $customerId;
    }

    /**
     * @param int $customerId
     */
    public function getWishlistCountByCustomerId($customerId)
    {
        $wishlist = $this->_wishlist->loadByCustomerId($customerId)->getItemCollection();
        return count($wishlist);
    }

    /*
    *function for creating VAT info in cart and checkout table header
    *PM MD 2020-06-22 see Requirements https://trello.com/c/vc14GwHF/26-magento-shops-mwst-mehrwertsteuer-tax-shipping-infos#comment-5eecb5ca9e3b6629950c6ced
    */
    public function getVatInfoPriceText()
    {

        /*Get Vat backend setting
        * 1: excluding tax
        * 2. incliding tax
        * 3. Including/excluding tax both*/
        $vatSetting= $this->getCartVatPriceInfo();

        //Check which Backendsettings are used and set right language key
        $vatInfoText="";
        if ($vatSetting == 1) {
            $vatInfoText = __('exkl. MwSt.');
        }
        if ($vatSetting == 2) {
            $vatInfoText = __('inkl. MwSt.');
        }

        //create html output string
        $return = "<p class='vat_info'>$vatInfoText</p>";

        //return html output
        return $return;
    }

    /**
     * function for Cart VAT info on PDP Page 2020 June 23 PV
     */
    public function getCartVatPriceInfo()
    {
        //get current storeID
        $storeID = $this->_storeManager->getStore()->getStoreId();
        return $this->getStoreConfig('tax/cart_display/price');
    }

    /*
    *function for creating VAT info in cart and checkout table header
    *PM MD 2020-06-22 see Requirements https://trello.com/c/vc14GwHF/26-magento-shops-mwst-mehrwertsteuer-tax-shipping-infos#comment-5eecb5ca9e3b6629950c6ced
    */
    public function getVatInfoSubtotalText()
    {

        /*Get Vat backend setting
        * 1: excluding tax
        * 2. incliding tax
        * 3. Including/excluding tax both*/
        $vatSetting= $this->getCartVatSubtotalInfo();

        //Check which Backendsettings are used and set right language key
        $vatInfoText="";
        if ($vatSetting == 1) {
            $vatInfoText = __('exkl. MwSt.');
        }
        if ($vatSetting == 2) {
            $vatInfoText = __('inkl. MwSt.');
        }

        //create html output string
        $return = "<p class='vat_info'>$vatInfoText</p>";

        //return html output
        return $return;
    }

    /**
     * function for Cart VAT info on PDP Page 2020 June 23 PV
     */
    public function getCartVatSubtotalInfo()
    {
        //get current storeID
        $storeID = $this->_storeManager->getStore()->getStoreId();
        return $this->getStoreConfig('tax/cart_display/subtotal');
    }

    /**
     * Get the group product parents attribute information on the cart page like ringmass,laenge
     * PM N.A 06.11.2020
     * referenced by Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    public function getParentInfoByChild($childId)
    {
        $parentsCollection = $this->productLinks->getParentIdsByChild(
            $childId,
            \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED
        );

        foreach ($parentsCollection as $parentId) {
            $parentsInformation = $this->_productModel->load($parentId);
            if ($parentsInformation->getTypeId() == 'grouped') {

                return $parentsInformation;

            }
        }
    }

    /**
     * get the product attribute package_type on the cart page of child product of group product
     * PM N.A 06.11.2020
     */
    public function getProductById($productId)
    {
        return $this->_productModel->load($productId);
    }

    //check the attribute set on the cart page and the checkout page
    public function getAttributeSetNameForBasePrice($_product)
    {

        $attributeSetRepository = $this->attributeSet->get($_product->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }
}
