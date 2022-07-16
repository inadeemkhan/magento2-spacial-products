<?php

/**
 * Upgrader for Product attributes
 *
 * @author Nitin Pant <nitin.pant@daffodilsw.com>
 *
 */

namespace DFA\SpecialProducts\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * App state for session
     *
     * @var State
     */
    protected $appState;

    /**
     * Constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->appState = $appState;
    }

    /**
     * Upgrades DB schema for the module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.1', '<')) {

            $installer = $setup;
            $installer->startSetup();
            $this->appState->setAreaCode('adminhtml');
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            //Yes/No Bestsellers
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'is_bestseller',
                [
                    'group' => 'Bestseller Settings',
                    'type' => 'int',
                    'label' => 'Is Bestseller',
                    'backend' => '',
                    'input' => 'boolean',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'used_in_product_listing' => true,
                    'visible_on_front' => false,
                ]
            );

            //Yes/No shown on homepage
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'is_bestseller_homepage',
                [
                    'group' => 'Bestseller Settings',
                    'type' => 'int',
                    'label' => 'Is Shown on Homepage',
                    'backend' => '',
                    'input' => 'boolean',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'used_in_product_listing' => true,
                    'visible_on_front' => false,
                ]
            );

            $installer->endSetup();
        }
    }
}
