<?php
namespace Vuefront\Brands\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Vuefront\Brands\Api\Data\BrandInterface;

/**
 * @api
 */
interface BrandRepositoryInterface
{
    /**
     * Save brand.
     *
     * @param  BrandInterface $brand
     * @return BrandInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BrandInterface $brand);

    /**
     * Retrieve Brand.
     *
     * @param  int $brandId
     * @return BrandInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $brandId);

    /**
     * Retrieve brands matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return \Vuefront\Brands\Api\Data\BrandSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Brand.
     *
     * @param  BrandInterface $brand
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BrandInterface $brand);

    /**
     * Delete Brand by ID.
     *
     * @param  int $brandId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($brandId);
}
