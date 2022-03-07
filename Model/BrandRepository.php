<?php
namespace Vuefront\Brands\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Vuefront\Brands\Api\BrandRepositoryInterface;

use Vuefront\Brands\Api\Data\BrandInterface;
use Vuefront\Brands\Api\Data\BrandInterfaceFactory;
use Vuefront\Brands\Api\Data\BrandSearchResultsInterface;
use Vuefront\Brands\Api\Data\BrandSearchResultsInterfaceFactory;
use Vuefront\Brands\Model\ResourceModel\Brand as ResourceBrand;
use Vuefront\Brands\Model\ResourceModel\Brand\Collection;
use Vuefront\Brands\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourceBrand
     */
    public $resource;
    /**
     * @var BrandCollectionFactory
     */
    public $brandCollectionFactory;

    /**
     * @var BrandSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    /**
     * @var BrandInterfaceFactory
     */
    public $brandInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * BrandRepository constructor.
     * @param ResourceBrand $resource
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandSearchResultsInterfaceFactory $brandSearchResultsInterfaceFactory
     * @param BrandInterfaceFactory $brandInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(
        ResourceBrand $resource,
        BrandCollectionFactory $brandCollectionFactory,
        BrandSearchResultsInterfaceFactory $brandSearchResultsInterfaceFactory,
        BrandInterfaceFactory $brandInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource                  = $resource;
        $this->brandCollectionFactory    = $brandCollectionFactory;
        $this->searchResultsFactory      = $brandSearchResultsInterfaceFactory;
        $this->brandInterfaceFactory     = $brandInterfaceFactory;
        $this->dataObjectHelper          = $dataObjectHelper;
        $this->hydrator                  = $hydrator ?: ObjectManager::getInstance()
            ->get(HydratorInterface::class);
    }
    /**
     * Save Brand.
     *
     * @param  BrandInterface $brand
     * @return BrandInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BrandInterface $brand)
    {
        /**
         * @var BrandInterface|\Magento\Framework\Model\AbstractModel $brand
         */
        try {
            $brandId = $brand->getId();
            if ($brandId && !($brand instanceof Brand && $brand->getOrigData())) {
                $brand = $this->hydrator->hydrate($this->getById($brandId), $this->hydrator->extract($brand));
            }

            $this->resource->save($brand);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the brand: %1',
                    $exception->getMessage()
                )
            );
        }
        return $brand;
    }

    /**
     * Retrieve Brand.
     *
     * @param  int $brandId
     * @return BrandInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $brandId)
    {
        if (!isset($this->instances[$brandId])) {
            /**
             * @var BrandInterface|\Magento\Framework\Model\AbstractModel $brand
             */
            $brand = $this->brandInterfaceFactory->create();
            $this->resource->load($brand, $brandId);

            if (!$brand->getId()) {
                throw new NoSuchEntityException(__('Requested brand doesn\'t exist'));
            }
            $this->instances[$brandId] = $brand;
        }

        return $this->instances[$brandId];
    }

    /**
     * Retrieve brands matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return BrandSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
         * @var BrandSearchResultsInterface $searchResults
         */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /**
         * @var \Vuefront\Brands\Model\ResourceModel\Brand\Collection $collection
         */
        $collection = $this->brandCollectionFactory->create();

        //Add filters from root filter group to the collection
        /**
         * @var FilterGroup $group
         */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            /**
             * @var SortOrder $sortOrder
             */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'brand_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /**
         * @var BrandInterface[] $categories
         */
        $brands = [];
        /**
         * @var \Vuefront\Brands\Model\Brand $brand
         */
        foreach ($collection as $brand) {
            /**
             * @var BrandInterface $brandDataObject
             */
            $brandDataObject = $this->brandInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $brandDataObject,
                $brand->getData(),
                BrandInterface::class
            );
            $categories[] = $brandDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($categories);
    }

    /**
     * Delete brand.
     *
     * @param  BrandInterface $brand
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BrandInterface $brand)
    {
        $id = $brand->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($brand);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Brand %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Brand by ID.
     *
     * @param  int $brandId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($brandId)
    {
        $brand = $this->getById($brandId);
        return $this->delete($brand);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param  FilterGroup $filterGroup
     * @param  Collection  $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }
}
