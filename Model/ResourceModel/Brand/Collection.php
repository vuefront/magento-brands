<?php

namespace Vuefront\Brands\Model\ResourceModel\Brand;

use Magento\Store\Model\StoreManagerInterface;
use \Vuefront\Brands\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Vuefront\Brands\Model\Brand::class,
            \Vuefront\Brands\Model\ResourceModel\Brand::class
        );
        $this->_map['fields']['brand_id'] = 'main_table.brand_id';
    }
}
