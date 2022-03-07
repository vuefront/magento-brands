<?php

namespace Vuefront\Brands\Model\ResourceModel;

class Brand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Brand construcor
     */
    protected function _construct()
    {
        $this->_init('vuefront_brands_brand', 'brand_id');
    }
}
