<?php declare(strict_types=1);

namespace ShrQuickViewModal\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductViewSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;
    private SessionInterface $session;

    public function __construct(
        SystemConfigService $systemConfigService,
        SessionInterface $session
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        if (!$this->systemConfigService->get('ShrQuickViewModal.config.enableRecentlyViewed')) {
            return;
        }

        $product = $event->getPage()->getProduct();
        $productId = $product->getId();

        $recentlyViewed = $this->session->get('shr_recently_viewed', []);
        
        if (($key = array_search($productId, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }

        array_unshift($recentlyViewed, $productId);
        
        $maxItems = $this->systemConfigService->get('ShrQuickViewModal.config.recentlyViewedCount') ?? 5;
        $recentlyViewed = array_slice($recentlyViewed, 0, $maxItems);

        $this->session->set('shr_recently_viewed', $recentlyViewed);
    }
}