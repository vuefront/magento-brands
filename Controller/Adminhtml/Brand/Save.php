<?php


namespace vuefront\brands\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Vuefront\Brands\Api\BrandRepositoryInterface;
use Vuefront\Brands\Api\Data\BrandInterface;
use Vuefront\Brands\Api\Data\BrandInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Vuefront\Brands\Model\Uploader;
use Vuefront\Brands\Model\UploaderPool;
use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Vuefront\Brands\Model\Brand as BrandModel;

class Save extends Action
{
    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;
    /**
     * @var BrandRepositoryInterface
     */
    public $brandRepository;

    /**
     * @var BrandInterfaceFactory
     */
    public $brandFactory;

    /**
     * @var BaseUrlRewrite
     */
    public $urlRewrite;

    /**
     * @var UrlRewriteService
     */
    public $urlRewriteService;

    /**
     * @var UrlFinderInterface
     */
    public $urlFinder;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var UrlRewriteFactory
     */
    public $urlRewriteFactory;

    /**
     * @var string
     */
    private $urlPrefix;

    /**
     * @var string
     */
    private $urlExtension;

    /**
     * Save constructor.
     *
     * @param UrlFinderInterface $urlFinder
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param BaseUrlRewrite $urlRewrite
     * @param UrlRewriteService $urlRewriteService
     * @param BrandRepositoryInterface $brandRepository
     * @param BrandInterfaceFactory $brandFactory
     * @param Context $context
     * @param UploaderPool $uploaderPool
     */
    public function __construct(
        UrlFinderInterface $urlFinder,
        UrlRewriteFactory $urlRewriteFactory,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        BaseUrlRewrite $urlRewrite,
        UrlRewriteService $urlRewriteService,
        BrandRepositoryInterface $brandRepository,
        BrandInterfaceFactory $brandFactory,
        Context $context,
        UploaderPool $uploaderPool
    ) {
        $this->urlRewriteService = $urlRewriteService;
        $this->urlRewrite = $urlRewrite;
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
        $this->brandFactory = $brandFactory;
        $this->brandRepository = $brandRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->uploaderPool = $uploaderPool;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlPrefix = BrandModel::URL_PREFIX;
        $this->urlExtension = BrandModel::URL_EXT;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $brand = null;
        $data = $this->getRequest()->getPostValue();
        $id = !empty($data['brand_id']) ? $data['brand_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $brand = $this->brandRepository->getById((int)$id);
            } else {
                unset($data['brand_id']);
                $brand = $this->brandFactory->create();
            }
            if ($data) {
                $brand->setTitle($data['title']);
                $brand->setDescription($data['description']);
                $brand->setMetaTitle($data['meta_title']);
                $brand->setMetaKeywords($data['meta_keywords']);
                $brand->setMetaDescription($data['meta_description']);
                $brand->setSortOrder($data['sort_order']);
                $brand->setKeyword($data['keyword']);

                if (!empty($data["keyword"])) {
                    $this->saveUrlRewrite(
                        $data["keyword"],
                        $brand->getId(),
                        $this->storeManager->getStore()->getId()
                    );
                }
            }
            $image = $this->getUploader('image-brand')->uploadFileAndGetName('image', $data);
            $brand->setImage($image);
            $this->brandRepository->save($brand);

            $this->messageManager->addSuccessMessage(__('You saved the Brand'));

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('vuefront_brands/brand/edit', ['brand_id' => $brand->getId()]);
            } else {
                $resultRedirect->setPath('vuefront_brands/brand');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($brand != null) {
                $this->storeCategoryDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $brand,
                        BrandInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_brands/brand/edit', ['brand_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($brand != null) {
                $this->storeCategoryDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $brand,
                        BrandInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_brands/brand/edit', ['brand_id' => $id]);
        }
        return $resultRedirect;
    }

    /**
     * Get Uploader
     *
     * @param string $type
     * @return Uploader
     * @throws \Exception
     */
    private function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }

    /**
     * Store Category Data To Session
     *
     * @param mixed $categoryData
     */
    private function storeCategoryDataToSession($categoryData)
    {
        $this->_getSession()->setVuefrontBrandsStoresData($categoryData);
    }
    /**
     * Saves the url rewrite for that specific store
     *
     * @param string $link
     * @param int $id
     * @param int $storeId
     * @return void
     */
    private function saveUrlRewrite($link, $id, $storeId)
    {
        $getCustomUrlRewrite = $this->urlPrefix . "/" . $link.$this->urlExtension;

        $brandId = $this->urlPrefix . "-" . $id;

        $filterData = [
            UrlRewriteService::STORE_ID => $storeId,
            UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
            UrlRewriteService::ENTITY_ID => $id,

        ];

        // check if there is an entity with same url and same id
        $rewriteFinder = $this->urlFinder->findOneByData($filterData);

        // if there is then do nothing, otherwise proceed
        if ($rewriteFinder === null) {
            // check maybe there is an old id with different url, in this case load the id and update the url
            $filterDataOldId = [
                UrlRewriteService::STORE_ID => $storeId,
                UrlRewriteService::ENTITY_TYPE => $brandId,
                UrlRewriteService::ENTITY_ID => $id
            ];
            $rewriteFinderOldId = $this->urlFinder->findOneByData($filterDataOldId);

            if ($rewriteFinderOldId !== null) {
                $this->urlRewriteFactory->create()->load($rewriteFinderOldId->getUrlRewriteId())
                    ->setRequestPath($getCustomUrlRewrite)
                    ->save();
            } else {
                // now we can save
                $this->urlRewriteFactory->create()
                    ->setStoreId($storeId)
                    ->setIdPath(rand(1, 100000))
                    ->setRequestPath($getCustomUrlRewrite)
                    ->setTargetPath("vuefront_brands/brand/view/index")
                    ->setEntityType($brandId)
                    ->setEntityId($id)
                    ->setIsAutogenerated(0)
                    ->save();
            }
        }
    }
}
