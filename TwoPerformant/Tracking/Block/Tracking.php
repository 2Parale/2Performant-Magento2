<?php
/**
 * Created by PhpStorm.
 * User: tiberiubuzdugan
 * Date: 04/12/2019
 * Time: 16:34
 */

namespace TwoPerformant\Tracking\Block;

class Tracking extends \Magento\Sales\Block\Order\Totals
{
    protected $checkoutSession;
    protected $_orderFactory;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
    }

    public function getOrder()
    {
        return  $this->_order = $this->_orderFactory->create()->loadByIncrementId(
            $this->checkoutSession->getLastRealOrderId());
    }

}