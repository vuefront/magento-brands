<?php

namespace Vuefront\Brands\Model\Config\Source;

/**
 * Used in edit brand form
 *
 */
class BrandTree implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Vuefront\Brands\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollectionFactory;

    /**
     * @var array
     */
    protected $_options;

    /**
     * BrandTree constructor.
     * @param \Vuefront\Brands\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory
     */
    public function __construct(
        \Vuefront\Brands\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory
    ) {
        $this->_brandCollectionFactory = $brandCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [];
        if (count($this->options) == 0) {
            $result = $this->_brandCollectionFactory->create();
            $result->load();
            $i = 0;
            foreach ($result->getItems() as $brand) {
                $newLine = $i != 0 ? '<br>' : '';
                $this->options[] = [
                    "value" => $brand->getId(),
                    "label" =>  $newLine.$brand->getTitle()
                ];
                $i++;
            }
        }
        return $this->options;
    }
}
