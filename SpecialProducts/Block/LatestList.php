<?php

namespace DFA\SpecialProducts\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class LatestList extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $_collection;

    protected $categoryRepository;

    protected $_resource;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->_collection = $collection;
        $this->_resource = $resource;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    protected function _getProductCollection()
    {
        return $this->getProducts();
    }

    public function getProducts()
    {
        $count = $this->getProductCount();
        $category_id = $this->getData("category_id");

        if(!$category_id) {
            $category_id = 1483;
        }

        $collection = clone $this->_collection;
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);

        if (!$category_id) {
            $category_id = $this->_storeManager->getStore()->getRootCategoryId();
        }

        $category = $this->categoryRepository->get($category_id);

        if (isset($category) && $category) {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToFilter('is_special_offer', 1)
                ->addAttributeToFilter('is_shown_on_homepage', 1)
                ->addCategoryFilter($category)
                ->addAttributeToSort('created_at', 'desc')
                ->addStoreFilter($this->_storeManager->getStore());
        } else {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToFilter('is_special_offer', 1)
                ->addAttributeToFilter('is_shown_on_homepage', 1)
                ->addAttributeToSort('created_at', 'desc')
                ->addStoreFilter($this->_storeManager->getStore());
        }

        $collection->getSelect()
            ->distinct(true)
            ->order('created_at', 'desc')
            ->limit($count);

        return $collection;
    }

    public function getLoadedProductCollection()
    {
        return $this->getProducts();
    }

    public function getProductCount()
    {
        $limit = $this->getData("product_count");
        if (!$limit)
            $limit = 10;
        return $limit;
    }
}
