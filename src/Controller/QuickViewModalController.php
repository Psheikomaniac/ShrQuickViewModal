<?php declare(strict_types=1);

namespace ShrQuickViewModal\Controller;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;

/**
 * @RouteScope(scopes={"storefront"})
 */
class QuickViewModalController extends StorefrontController
{
    private $productRepository;
    private $systemConfigService;

    public function __construct(
        SalesChannelRepositoryInterface $productRepository,
        SystemConfigService $systemConfigService
    ) {
        $this->productRepository = $productRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route("/shr/quick-view/modal/{productId}", name="frontend.shr.quick-view.modal", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function showQuickViewModal(string $productId, Request $request, SalesChannelContext $context): Response
    {
        // Create criteria for product search
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $productId));
        
        // Add necessary associations based on configuration
        $criteria->addAssociation('media');
        $criteria->addAssociation('options.group');
        $criteria->addAssociation('properties.group');
        
        if ($this->systemConfigService->get('ShrQuickViewModal.config.showTechnicalData')) {
            $criteria->addAssociation('manufacturer');
            $criteria->addAssociation('properties');
        }
        
        if ($this->systemConfigService->get('ShrQuickViewModal.config.showReviews')) {
            $criteria->addAssociation('reviews');
        }
        
        // Get product from repository
        $product = $this->productRepository->search($criteria, $context)->first();
        
        if (!$product instanceof SalesChannelProductEntity) {
            throw new \RuntimeException(sprintf('Product with id "%s" not found.', $productId));
        }
        
        // Get configuration settings
        $imageQuality = $this->systemConfigService->get('ShrQuickViewModal.config.imageQuality') ?? 'medium';
        $compareEnabled = $this->systemConfigService->get('ShrQuickViewModal.config.compareEnabled') ?? false;
        $maxCompareProducts = $this->systemConfigService->get('ShrQuickViewModal.config.maxCompareProducts') ?? 4;
        
        // Get recently viewed products if enabled
        $recentlyViewedProducts = [];
        if ($this->systemConfigService->get('ShrQuickViewModal.config.enableRecentlyViewed')) {
            $recentlyViewed = $this->systemConfigService->get('ShrQuickViewModal.config.recentlyViewed') ?? [];
            if (!empty($recentlyViewed)) {
                $recentlyCriteria = new Criteria();
                $recentlyCriteria->setIds($recentlyViewed);
                $recentlyCriteria->addAssociation('cover');
                $recentlyViewedProducts = $this->productRepository->search($recentlyCriteria, $context);
            }
        }
        
        return $this->renderStorefront('@ShrQuickViewModal/storefront/quick-view/modal.html.twig', [
            'product' => $product,
            'imageQuality' => $imageQuality,
            'compareEnabled' => $compareEnabled,
            'maxCompareProducts' => $maxCompareProducts,
            'recentlyViewedProducts' => $recentlyViewedProducts,
            'showTechnicalData' => $this->systemConfigService->get('ShrQuickViewModal.config.showTechnicalData'),
            'showReviews' => $this->systemConfigService->get('ShrQuickViewModal.config.showReviews'),
            'showShippingInfo' => $this->systemConfigService->get('ShrQuickViewModal.config.showShippingInfo'),
            'improvedA11y' => $this->systemConfigService->get('ShrQuickViewModal.config.improvedA11y')
        ]);
    }
}