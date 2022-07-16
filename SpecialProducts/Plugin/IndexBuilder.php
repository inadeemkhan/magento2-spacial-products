<?php

namespace DFA\SpecialProducts\Plugin;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\Filter\BoolExpression;
use Magento\Framework\Search\Request\Query\Filter;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\App\ResourceConnection;

class IndexBuilder
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;


    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productVisibility = $productVisibility;
        $this->categoryHelper = $categoryHelper;
        $this->registry = $registry;
    }

    /**
     * Build index query
     *
     * @param $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundBuild($subject, callable $proceed, RequestInterface $request)
    {
        $select = $proceed($request);

        $productUniqueIds = $this->getCustomCollectionQuery();

        if (count($productUniqueIds) > 0) {
            $select->where('search_index.entity_id IN (' . join(',', $productUniqueIds) . ')');
        }

        return $select;
    }

    /**
     *
     * @return ProductIds[]
     */
    public function getCustomCollectionQuery()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect(array('entity_id', 'sku', 'is_special_offer', 'is_bestseller'));

        // set visibility filter
        $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());

        $category = $this->registry->registry('current_category');

        if (isset($category)) {
            $categoryId = $category->getId();
            if ($categoryId == 1452) {
                $collection->addAttributeToFilter('is_special_offer', 1);
            } else if ($categoryId == 99) {
                $collection->addAttributeToFilter('is_bestseller', 1);
            }
            $collection->addCategoryFilter($category);
        }

        $getProductAllIds = $collection->getAllIds();

        return $getProductAllIds;
    }
}
