<?php
namespace Vuefront\Brands\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vuefront\Brands\Api\BrandRepositoryInterface;
use Vuefront\Brands\Api\Data\BrandInterfaceFactory;

class MassDelete extends Action
{
    /**
     * @var BrandRepositoryInterface
     */
    public $brandRepository;

    /**
     * @var BrandInterfaceFactory
     */
    public $brandFactory;

    /**
     * MassDelete constructor.
     * @param BrandRepositoryInterface $brandRepository
     * @param BrandInterfaceFactory $brandFactory
     * @param Context $context
     */
    public function __construct(
        BrandRepositoryInterface $brandRepository,
        BrandInterfaceFactory $brandFactory,
        Context $context
    ) {
        $this->brandFactory = $brandFactory;
        $this->brandRepository = $brandRepository;
        parent::__construct($context);
    }
    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('brand_id');
        $data = $this->getRequest()->getPostValue();
        if (!empty($data['selected'])) {
            try {
                foreach ($data['selected'] as $value) {
                    $this->categoryRepository->deleteById($value);
                }
                $this->messageManager->addSuccessMessage(__('The Brand has been deleted.'));
                $resultRedirect->setPath('vuefront_brands/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Brand no longer exists.'));
                return $resultRedirect->setPath('vuefront_brands/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('vuefront_brands/brand/edit', ['brand_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Brand'));
                return $resultRedirect->setPath('vuefront_brands/brand/edit', ['brand_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Brand to delete.'));
        $resultRedirect->setPath('vuefront_brands/*/');
        return $resultRedirect;
    }
}
