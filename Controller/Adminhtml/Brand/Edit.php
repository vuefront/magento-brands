<?php

namespace Vuefront\Brands\Controller\Adminhtml\Brand;

use Vuefront\Brands\Api\BrandRepositoryInterface;
use Vuefront\Brands\Controller\RegistryConstants;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Brand factory
     *
     * @var BrandRepositoryInterface
     */
    public $brandRepository;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param BrandRepositoryInterface $brandRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        BrandRepositoryInterface $brandRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->brandRepository = $brandRepository;
    }

    /**
     * Is Allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vuefront_Brands::brand_edit');
    }

    /**
     * Init actions.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vuefront_Brands::brand')
            ->addBreadcrumb(__('Brand'), __('Brand'))
            ->addBreadcrumb(__('Manage Brands'), __('Manage Brands'));

        return $resultPage;
    }

    /**
     * Init Brand
     *
     * @return mixed
     */
    private function _initBrand()
    {
        $brandId = $this->getRequest()->getParam('brand_id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_BRAND_ID, $brandId);

        return $brandId;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $brandId = $this->_initBrand();

        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('vuefront_brands::brand');
        $resultPage->getConfig()->getTitle()->prepend(__('Brand'));
        $resultPage->addBreadcrumb(__('Brand'), __('Brand'), $this->getUrl('vuefront_brands/brand'));

        if ($brandId === null) {
            $resultPage->addBreadcrumb(__('New Brand'), __('New Brand'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Brand'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Brand'), __('Edit Brand'));

            $resultPage->getConfig()->getTitle()->prepend(
                $this->brandRepository->getById($brandId)->getTitle()
            );
        }

        return $resultPage;
    }
}
