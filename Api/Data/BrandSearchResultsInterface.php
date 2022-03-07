<?php

namespace Vuefront\Brands\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Vuefront\Brands\Api\Data\BrandInterface;

/**
 * @api
 */
interface BrandSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Stores list.
     *
     * @return BrandInterface[]
     */
    public function getItems();

    /**
     * Set Stores list.
     *
     * @param  BrandInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
