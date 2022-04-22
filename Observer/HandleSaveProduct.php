<?php
namespace Vuefront\Brands\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Vuefront\Brands\Model\ResourceModel\Brand as ResourceBrand;

class HandleSaveProduct implements ObserverInterface
{
    protected $request;

    /**
     * @var ResourceBrand
     */
    protected $_resourceBrand;

    /**
     * HandleSaveProduct constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        ResourceBrand $resourceBrand,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
        $this->_resourceBrand = $resourceBrand;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->request->getParams();
        if (!isset($params['vfbrands'])) {
            return;
        }
        $brandData = $params['vfbrands'];
        $_product = $observer->getProduct();  // you will get product object
        $product_id = (int)$_product->getId(); // for product id
        $brandId = (int)$brandData['brandId'];

        $table = $this->_resourceBrand->getTable('vuefront_brands_brand_product');
        $this->_resourceBrand->getConnection()->delete($table, "product_id=".$product_id);
        $this->_resourceBrand->getConnection()->insert($table, [
            'brand_id'=> $brandId,
            'product_id' => $product_id
        ]);
    }
}
