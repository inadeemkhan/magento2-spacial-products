<?php
/**
 * Installer for Product attributes
 *
 * @author Nitin Pant <nitin.pant@daffodilsw.com>
 *
 */

namespace DFA\SpecialProducts\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
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
     * Installs DB schema for the module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $installer = $setup;
        $installer->startSetup();
        $this->appState->setAreaCode('adminhtml');
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        //Yes/No
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 
            'is_special_offer',
            [
                'group' => 'Special Offer Settings',
                'type' => 'int',
                'label' => 'Is on special offer',
                'backend' => '',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
            ]
        );

        //Yes/No
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_shown_on_homepage',
            [
                'group' => 'Special Offer Settings',
                'type' => 'int',
                'label' => 'Shown on Homepage',
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
