<?php
/**
 * Ajax controller file to process the itemn qty update from the checkout page direct
 * PM KL, @link https://projects.zoho.com/portal/pixelmechanics2#taskdetail/1781812000000140023/1781812000000179041/1781812000000501041
 */

namespace Pixelmechanics\Checkout\Controller\Ajax;
 
use Magento\Checkout\Model\Sidebar;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
 
class CartUpdate extends Action
{
    /**
     * @var Sidebar
     */
    protected $sidebar;
 
    /**
     * @var LoggerInterface
     */
    protected $logger;
 
    /**
     * @var Data
     */
    protected $jsonHelper;
 
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Cart
     */
    protected $_checkoutSession;
 
    /**
     * @param Context $context
     * @param Sidebar $sidebar
     * @param LoggerInterface $logger
     * @param Data $jsonHelper
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Cart $cart,
        Session $checkoutSession,
        Sidebar $sidebar,
        LoggerInterface $logger,
        Data $jsonHelper
    ) {
        $this->sidebar = $sidebar;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->cart = $cart;
        parent::__construct($context);
    }
 
    /**
     * @return $this
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $itemQty = (int)$this->getRequest()->getParam('item_qty');
 
        try {
            $this->updateQuoteItem($itemId, $itemQty);
            return $this->jsonResponse();
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }
 
    /**
     * Update quote item
     *
     * @param int $itemId
     * @param int $itemQty
     * @throws LocalizedException
     * @return $this
     */
    public function updateQuoteItem($itemId, $itemQty)
    {
        $itemData = [$itemId => ['qty' => $itemQty]];
        $this->cart->updateItems($itemData)->save();
    }
 
    /**
     * Get quote object associated with cart. By default it is current customer session quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }
 
 
    /**
     * Compile JSON response
     *
     * @param string $error
     * @return Http
     */
    protected function jsonResponse($error = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($this->sidebar->getResponseData($error))
        );
    }
}
