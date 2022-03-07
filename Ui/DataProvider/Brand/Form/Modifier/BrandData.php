<?php
namespace Vuefront\Brands\Ui\DataProvider\Brand\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Vuefront\Brands\Model\ResourceModel\Brand\CollectionFactory;

class BrandData implements ModifierInterface
{
    /**
     * @var \Vuefront\Brands\Model\ResourceModel\Brand\Collection
     */
    public $collection;

    /**
     * BrandData constructor.
     *
     * @param CollectionFactory $brandCollectionFactory
     */
    public function __construct(
        CollectionFactory $brandCollectionFactory
    ) {
        $this->collection = $brandCollectionFactory;
    }

    /**
     * Get Collection
     *
     * @return \Vuefront\Brands\Model\ResourceModel\Brand\Collection
     */
    public function getCollection()
    {
        return $this->collection->create();
    }

    /**
     * Modify Meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Modify Data
     *
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $collection = $this->getCollection();

        $items = $collection->getItems();
        /**
         * @var $brand \Vuefront\Brands\Model\Brand
         */
        foreach ($items as $brand) {
            $_data = $brand->getData();
            if (isset($_data['image'])) {
                $image = [];
                $image[0]['name'] = $brand->getImage();
                $image[0]['url'] = $brand->getImageUrl();
                $_data['image'] = $image;
            }
            $brand->setData($_data);
            $data[$brand->getId()] = $_data;
        }

        return $data;
    }
}
