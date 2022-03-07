<?php

namespace Vuefront\Brands\Model;

use Vuefront\Brands\Api\Data\BrandInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Brand extends \Magento\Framework\Model\AbstractModel implements BrandInterface
{

    public const BASE_TMP_PATH='vuefront_brands/tmp/brand/image';

    public const BASE_PATH='vuefront_brands/brand/image';

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * Url Prefix
     *
     * @var string
     */
    public const URL_PREFIX = 'vuefront-brands-brand';

    /**
     * Url extension
     *
     * @var string
     */
    public const URL_EXT = '.html';

    /**
     * Category constructor.
     *
     * @param UploaderPool $uploaderPool
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        UploaderPool $uploaderPool,
        Context $context,
        Registry $registry,
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        $this->uploaderPool = $uploaderPool;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(\Vuefront\Brands\Model\ResourceModel\Brand::class);
    }

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(BrandInterface::BRAND_ID);
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(BrandInterface::TITLE);
    }

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(BrandInterface::DESCRIPTION);
    }

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData(BrandInterface::IMAGE);
    }

    /**
     * Get Keyword
     *
     * @return string|null
     */
    public function getKeyword()
    {
        return $this->getData(BrandInterface::KEYWORD);
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        $url_key = $this->getKeyword();

        return self::URL_PREFIX.'/'.$url_key.self::URL_EXT;
    }

    /**
     * Get Meta Title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->getData(BrandInterface::META_TITLE);
    }

    /**
     * Get Meta Keywords
     *
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->getData(BrandInterface::META_KEYWORDS);
    }

    /**
     * Get Meta Description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(BrandInterface::META_DESCRIPTION);
    }

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->getData(BrandInterface::SORT_ORDER);
    }

    /**
     * Get Date Added
     *
     * @return string|null
     */
    public function getDateAdded()
    {
        return $this->getData(BrandInterface::DATE_ADDED);
    }

    /**
     * Get Date Modified
     *
     * @return string|null
     */
    public function getDateModified()
    {
        return $this->getData(BrandInterface::DATE_MODIFIED);
    }
    /**
     *  Get Image Path url
     *
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image-brand');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * Set Id
     *
     * @param int|mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(BrandInterface::BRAND_ID, $id);
        return $this;
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData(BrandInterface::TITLE, $title);
        return $this;
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->setData(BrandInterface::DESCRIPTION, $description);
        return $this;
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->setData(BrandInterface::IMAGE, $image);
        return $this;
    }

    /**
     * Set Keyword
     *
     * @param string $keyword
     * @return $this
     */
    public function setKeyword($keyword)
    {
        $this->setData(BrandInterface::KEYWORD, $keyword);
        return $this;
    }

    /**
     * Set Meta Title
     *
     * @param string $meta_title
     * @return $this
     */
    public function setMetaTitle($meta_title)
    {
        $this->setData(BrandInterface::META_TITLE, $meta_title);
        return $this;
    }

    /**
     * Set Meta Keywords
     *
     * @param string $meta_keywords
     * @return $this
     */
    public function setMetaKeywords($meta_keywords)
    {
        $this->setData(BrandInterface::META_KEYWORDS, $meta_keywords);
        return $this;
    }

    /**
     * Set Meta Description
     *
     * @param string $meta_description
     * @return $this
     */
    public function setMetaDescription($meta_description)
    {
        $this->setData(BrandInterface::META_DESCRIPTION, $meta_description);
        return $this;
    }

    /**
     * Set Sort Order
     *
     * @param int $sort_order
     * @return $this
     */
    public function setSortOrder($sort_order)
    {
        $this->setData(BrandInterface::SORT_ORDER, $sort_order);
        return $this;
    }

    /**
     * Set Date Added
     *
     * @param string $date_added
     * @return $this
     */
    public function setDateAdded($date_added)
    {
        $this->setData(BrandInterface::DATE_ADDED, $date_added);
        return $this;
    }

    /**
     * Set Date Modified
     *
     * @param string $date_modified
     * @return $this
     */
    public function setDateModified($date_modified)
    {
        $this->setData(BrandInterface::DATE_MODIFIED, $date_modified);
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['vuefront_brands_brand' . '_' . $this->getId()];
    }
}
