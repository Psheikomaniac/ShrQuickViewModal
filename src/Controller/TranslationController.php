<?php

namespace Shr\QuickViewModal\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class TranslationController extends AbstractController
{
    /**
     * @Route("/theme/quick-view-modal/snippets/{locale}.json", name="frontend.quick.view.modal.snippets", methods={"GET"})
     */
    public function getSnippets(string $locale): Response
    {
        $file = __DIR__ . '/../Resources/snippet/' . str_replace('-', '_', $locale) . '/messages.' . $locale . '.json';
        
        if (!file_exists($file)) {
            // Fallback to English if requested locale doesn't exist
            $file = __DIR__ . '/../Resources/snippet/en_GB/messages.en-GB.json';
        }
        
        if (!file_exists($file)) {
            return new JsonResponse(['error' => 'Translation file not found'], Response::HTTP_NOT_FOUND);
        }
        
        $content = file_get_contents($file);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/json');
        
        // Cache for 1 hour
        $response->setPublic();
        $response->setMaxAge(3600);
        
        return $response;
    }
}