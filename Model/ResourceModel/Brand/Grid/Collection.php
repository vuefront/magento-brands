<?php

namespace Vuefront\Brands\Model\ResourceModel\Brand\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Vuefront\Brands\Model\ResourceModel\Brand;
use Vuefront\Brands\Model\ResourceModel\Brand\Collection as BrandCollection;

class Collection extends BrandCollection implements SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface
     */
    protected $aggregations;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Document::class, Brand::class);
    }

    /**
     * Get Aggregations
     *
     * @return \Magento\Framework\Api\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Set Aggregations
     *
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     * @return void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get Search Criteria
     *
     * @return SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set Search Criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get Total Count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set Total Count
     *
     * @param  mixed $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set Items
     *
     * @param array|null $items
     *
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
