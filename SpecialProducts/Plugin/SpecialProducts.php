<?php

namespace DFA\SpecialProducts\Plugin;

use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Block\Product\ListProduct;

class SpecialProducts
{
    /**
     * Core Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Init
     *
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Registry $registry,
        LoggerInterface $logger
    ) {
        $this->registry = $registry;
        $this->logger = $logger;
    }

    /**
     * Plugin
     *
     * @param ListProduct $subject
     * @param array $additional
     *
     * @return void
     */
    public function afterGetLoadedProductCollection(
        ListProduct $subject,
        $result
    ) {

        $category = $this->registry->registry('current_category');

        if (isset($category)) {
            $categoryId = $category->getId();
            if ($categoryId == 1452) {
                $result->addAttributeToFilter('is_special_offer', 1);
            }
        }

        return $result;
    }
}
