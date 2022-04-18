<?php

namespace Vuefront\Brands\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\UI\Component\Form\Element\Select;
use Magento\UI\Component\Form\Field;
use Magento\UI\Component\Form\Fieldset;
use Vuefront\Brands\Model\Config\Source\BrandTree;
use Vuefront\Brands\Model\ResourceModel\Brand as ResourceBrand;

class Fields extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;
    /**
     * @var BrandTree
     */
    private $_brandTree;
    /**
     * @var ResourceBrand
     */
    protected $_resourceBrand;
    public function __construct(
        ResourceBrand $resourceBrand,
        LocatorInterface $locator,
        BrandTree $brandTree
    ) {
        $this->_resourceBrand = $resourceBrand;
        $this->locator = $locator;
        $this->_brandTree = $brandTree;
    }
    protected function getFields()
    {
        return [
            'brandId'   => [
                'arguments' => [
                    'data' => [
                        'options' => $this->_brandTree->toOptionArray(),
                        'config' => [
                            'label' => __('Brand'),
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'component' => 'Magento_Catalog/js/components/new-category',
                            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                            'dataScope' => 'brandId',
                            'filterOptions' => true,
                            'chipsEnabled' => true,
                            'showCheckbox' => false,
                            'disableLabel' => true,
                            'multiple' => false,
                            'levelsVisibility' => 1,
                            'required' => true,
                            'sortOrder' => 10,
                        ]
                    ]
                ]
            ],
        ];
    }

    public function modifyData(array $data)
    {
        $product   = $this->locator->getProduct();
        $productId = (int)$product->getId();
        $table = $this->_resourceBrand->getTable('vuefront_brands_brand_product');
        $brandData = $this->_resourceBrand->getConnection()->
            select()->
            from($table)->
            where('product_id='.$productId)->
            query()->
            fetch();
        $brandId = 0;
        if ($brandData) {
            $brandId = $brandData['brand_id'];
        }

        $data = array_replace_recursive(
            $data,
            [
                $productId => [
                    'vfbrands' => [
                        'brandId'=> $brandId
                    ]
                ]
            ]
        );
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'vfbrands' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('VueFront Brands'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => 'data.vfbrands',
                                'sortOrder' => 10
                            ],
                        ],
                    ],
                    'children' => $this->getFields()
                ],
            ]
        );

        return $meta;
    }
}
