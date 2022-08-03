<?php

namespace I95Dev\CustomerRedirecting\Observer;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Uri Validator
     *
     * @var \Zend\Validator\Uri
     */
    protected $uri;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Zend\Validator\Uri $uri,
        \Magento\Framework\App\ResponseFactory $responseFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->uri = $uri;
        $this->responseFactory = $responseFactory;
    }

   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $redirectDashboard = $this->scopeConfig->isSetFlag(
            'customer/startup/redirect_dashboard',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
        );

        // if the Redirect Customer to Account Dashboard after Logging in set to "No"
        if (!$redirectDashboard) {
            $customPage = $this->scopeConfig->getValue(
                'customer/startup/custom_page_for_redirecting',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
            );
            // If the custom page is set and it is a URL valid.
            if (!empty($customPage) && $this->uri->isValid($customPage)) {
                $resultRedirect = $this->responseFactory->create();
                // Redirect to the custom page.
                $resultRedirect->setRedirect($customPage)->sendResponse('200');
                exit();
            }
        }
    }
}
