<?php

namespace Vuefront\Brands\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Vuefront\Brands\Block\Adminhtml\Brand\Grid\Renderer\Action\UrlBuilder;

class BrandActions extends Column
{
    /** Url Path */
    public const BRANDS_URL_PATH_EDIT = 'vuefront_brands/brand/edit';
    public const BRANDS_URL_PATH_DELETE = 'vuefront_brands/brand/delete';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

     /**
      * @param ContextInterface   $context
      * @param UiComponentFactory $uiComponentFactory
      * @param UrlBuilder         $actionUrlBuilder
      * @param UrlInterface       $urlBuilder
      * @param array              $components
      * @param array              $data
      * @param [type]             $editUrl
      */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::BRANDS_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['brand_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['brand_id' => $item['brand_id']]),
                        'label' => __('Edit'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}
