<?php declare(strict_types=1);

namespace ShrQuickViewModal\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProductCompareController extends StorefrontController
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
     * @Route("/shr/product-compare", name="frontend.shr.product-compare", methods={"GET"})
     */
    public function showCompare(Request $request, SalesChannelContext $context): Response
    {
        $productIds = $request->query->get('ids', []);
        if (!is_array($productIds)) {
            $productIds = explode(',', $productIds);
        }

        $criteria = new Criteria();
        $criteria->setIds($productIds);
        $criteria->addAssociation('media');
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('properties.group');

        $products = $this->productRepository->search($criteria, $context);

        return $this->renderStorefront('@ShrQuickViewModal/storefront/page/compare/index.html.twig', [
            'products' => $products,
            'showTechnicalData' => $this->systemConfigService->get('ShrQuickViewModal.config.showTechnicalData'),
            'imageQuality' => $this->systemConfigService->get('ShrQuickViewModal.config.imageQuality') ?? 'medium'
        ]);
    }
}