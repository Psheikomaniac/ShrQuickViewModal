<?php declare(strict_types=1);

namespace ShrQuickViewModal\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Shopware\Storefront\Page\Listing\ListingPageLoadedEvent;
use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class ProductListingSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;
    private Environment $twig;

    public function __construct(SystemConfigService $systemConfigService, Environment $twig)
    {
        $this->systemConfigService = $systemConfigService;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ListingPageLoadedEvent::class => 'onListingLoaded',
            SearchPageLoadedEvent::class => 'onListingLoaded',
            ProductPageLoadedEvent::class => 'onProductPageLoaded'
        ];
    }

    public function onListingLoaded($event): void
    {
        // Check if the plugin is active
        $active = $this->systemConfigService->get('ShrQuickViewModal.config.active');
        if (!$active) {
            return;
        }

        // Add plugin config to the page
        $page = $event->getPage();
        $page->addExtension('shr_quick_view_config', [
            'showOnHoverOnly' => $this->systemConfigService->get('ShrQuickViewModal.config.showOnHoverOnly'),
            'buttonIcon' => $this->systemConfigService->get('ShrQuickViewModal.config.buttonIcon'),
            'buttonColor' => $this->systemConfigService->get('ShrQuickViewModal.config.buttonColor'),
            'showGallery' => $this->systemConfigService->get('ShrQuickViewModal.config.showGallery'),
            'showShortDescription' => $this->systemConfigService->get('ShrQuickViewModal.config.showShortDescription'),
            'showTechnicalData' => $this->systemConfigService->get('ShrQuickViewModal.config.showTechnicalData'),
            'showReviews' => $this->systemConfigService->get('ShrQuickViewModal.config.showReviews'),
            'showShippingInfo' => $this->systemConfigService->get('ShrQuickViewModal.config.showShippingInfo'),
            'improvedA11y' => $this->systemConfigService->get('ShrQuickViewModal.config.improvedA11y'),
            'compareEnabled' => $this->systemConfigService->get('ShrQuickViewModal.config.compareEnabled'),
            'maxCompareProducts' => $this->systemConfigService->get('ShrQuickViewModal.config.maxCompareProducts'),
            'imageQuality' => $this->systemConfigService->get('ShrQuickViewModal.config.imageQuality')
        ]);
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        if (!$this->systemConfigService->get('ShrQuickViewModal.config.enableRecentlyViewed')) {
            return;
        }

        $product = $event->getPage()->getProduct();
        $productId = $product->getId();

        $recentlyViewed = $this->getRecentlyViewedProducts();
        
        if (($key = array_search($productId, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }

        array_unshift($recentlyViewed, $productId);
        
        $maxItems = $this->systemConfigService->get('ShrQuickViewModal.config.recentlyViewedCount') ?? 5;
        $recentlyViewed = array_slice($recentlyViewed, 0, $maxItems);

        $this->setRecentlyViewedProducts($recentlyViewed);
    }

    private function getRecentlyViewedProducts(): array
    {
        return $this->systemConfigService->get('ShrQuickViewModal.config.recentlyViewed') ?? [];
    }

    private function setRecentlyViewedProducts(array $products): void
    {
        $this->systemConfigService->set('ShrQuickViewModal.config.recentlyViewed', $products);
    }
}