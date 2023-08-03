<?php

declare(strict_types=1);

namespace DistanceRateShipping;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollection;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryCollection;
use Shopware\Core\Checkout\Cart\Delivery\Struct\ShippingMethodEntity;
use Shopware\Core\Checkout\Cart\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceRuleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\QuantityPriceDefinition;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;

class DistanceRateShipping extends Plugin
{
    // the id of the custom shipping method
    public const SHIPPING_METHOD_ID = '207d07178bf4402ba55de9ce844c8793';


    // the id of the currency for the custom shipping method
    public const CURRENCY_ID = 'b7d2554b0ce847cd82f3ac9bd1c0dfca'; // EUR

    // the name of the custom shipping method
    public const SHIPPING_METHOD_NAME = 'Distance Rate Shipping';

    // the description of the custom shipping method
    public const SHIPPING_METHOD_DESCRIPTION = 'Shipping rate is calculated based on the distance';

    // the key for storing the custom shipping method in the data collection
    public const SHIPPING_METHOD_KEY = 'distance_rate_shipping_method';

    /**
    * @var EntityRepositoryInterface
    */
    private $shippingMethodRepository;

    /**
    * @var EntityRepositoryInterface
    */
    private $ruleRepository;

    /**
    * @var EntityRepositoryInterface
    */
    private $deliveryTimeRepository;

    /**
    * @var EntityRepositoryInterface
    */
    private $salesChannelRepository;

    /**
    * @var string
    */
    private $deliveryTimeId;

    /**
    * @var string
    */
    private $avialabilityRuleId;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->container = $container;


    }

    public function install(InstallContext $context): void
    {
        parent::install($context);


        $salesChannelContext = $context->getContext();

        // get the repositories for shipping method, rule and sales channel entities
        $this->shippingMethodRepository = $this->container->get('shipping_method.repository');
        $this->salesChannelRepository = $this->container->get('sales_channel.repository');
        $this->ruleRepository = $this->container->get('rule.repository');
        $this->deliveryTimeRepository = $this->container->get('delivery_time.repository');
        $this->avialabilityRuleId = $this->ruleRepository->search(
            (new Criteria()),
            $salesChannelContext
        )->first()->getId();

        $this->deliveryTimeId = $this->deliveryTimeRepository->search(
            (new Criteria()),
            $salesChannelContext
        )->first()->getId();
        // create a new shipping method entity and persist it to the database
        $this->createShippingMethod($salesChannelContext);

        // assign the custom shipping method to all sales channels
        //$this->assignNewShippingMethodToSalesChannels();
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        // Get the sales channel context from the install context
        $salesChannelContext = $context->getContext();

        $this->shippingMethodRepository = $this->container->get('shipping_method.repository');
        // delete the custom shipping method entity from the database
        $this->deleteShippingMethod($salesChannelContext);

        $this->dropRatesTable($context);
        // delete the availability rule entity from the database

    }



    private function createShippingMethod($salesChannelContext): void
    {
        // create a new shipping method entity with basic information, availability rule and price matrix
        $shippingMethod = [
        'id' => self::SHIPPING_METHOD_ID,
        'name' => self::SHIPPING_METHOD_NAME,
        'description' => self::SHIPPING_METHOD_DESCRIPTION,
        'active' => true,
        'availabilityRuleId' => $this->avialabilityRuleId,
        "deliveryTimeId" => $this->deliveryTimeId,
        'prices' => [
        [
        'currencyId' => self::CURRENCY_ID,
        'quantityStart' => 1,
        'price' => 0.00, // fixed price for demonstration, can be dynamic based on logic
        ],
        ],
        ];

        // add the shipping method to the shipping method repository
        $this->shippingMethodRepository->create([$shippingMethod], $salesChannelContext);
    }

    private function deleteShippingMethod($salesChannelContext): void
    {
        // delete the shipping method from the shipping method repository by id
        $this->shippingMethodRepository->delete([['id' => self::SHIPPING_METHOD_ID]], $salesChannelContext);
    }


    public function assignNewShippingMethodToSalesChannels(): void
    {
        $context = Context::createDefaultContext();
        // Get the new shipping method entity by its ID
        $newShippingMethod = $this->shippingMethodRepository->search(
            (new Criteria([self::SHIPPING_METHOD_ID])),
            $context
        )->first();


        if (!$newShippingMethod) {
            return;
        }

        $newShippingMethod->setName(self::SHIPPING_METHOD_NAME);
        $newShippingMethod->setAvailabilityRuleId(self::AVAILABILITY_RULE_ID);
        $newShippingMethod->setDeliveryTimeId(self::DELIVERY_TIME_ID);
        // Fetch all sales channels
        $criteria = new Criteria();
        $criteria->addAssociation('shippingMethods');

        $salesChannels = $this->salesChannelRepository->search($criteria, $context)->getEntities();

        // Prepare the data for updating sales channels
        $salesChannelData = [];
        foreach ($salesChannels as $salesChannel) {

            $shippingMethods = $salesChannel->getShippingMethods();

            $shippingMethods->add($newShippingMethod);

            $salesChannelData[] = [
                'id' => $salesChannel->getId(),
                'shippingMethods' => $shippingMethods,
            ];
        }
        // Update the sales channels
        $this->salesChannelRepository->update($salesChannelData, $context);
    }



    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // get the custom shipping method from the shipping method repository by id
        $shippingMethod = $this->shippingMethodRepository->search(new Criteria([self::SHIPPING_METHOD_ID]), $context->getContext())->first();

        // store the shipping method in the data collection under a unique key
        $data->set(self::SHIPPING_METHOD_KEY, $shippingMethod);
    }

    public function dropRatesTable($context)
    {
        if ($context->keepUserData()) {
            return;
        }
        $this->container->get('Doctrine\DBAL\Connection')->executeStatement(
            'DROP TABLE `distance_rate`;'
        );
    }
}
