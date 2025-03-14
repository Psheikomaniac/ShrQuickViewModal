<?php

namespace Shr\QuickViewModal\Service;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Language\LanguageEntity;

class TranslationHelper
{
    private EntityRepository $languageRepository;
    private array $fallbackLocales = [
        'en-GB',
        'de-DE',
        'es-ES',
        'pt-PT',
        'zh-CN'
    ];

    public function __construct(EntityRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function getLocale(Context $context): string
    {
        $criteria = new Criteria([$context->getLanguageId()]);
        $criteria->addAssociation('locale');
        
        /** @var LanguageEntity|null $language */
        $language = $this->languageRepository->search($criteria, $context)->first();
        
        if ($language && $language->getLocale()) {
            return $language->getLocale()->getCode();
        }
        
        return 'en-GB';
    }

    public function getFallbackLocale(string $currentLocale): string
    {
        $index = array_search($currentLocale, $this->fallbackLocales);
        if ($index !== false && isset($this->fallbackLocales[$index + 1])) {
            return $this->fallbackLocales[$index + 1];
        }
        
        return 'en-GB';
    }

    public function getTranslation(string $key, array $parameters = [], SalesChannelContext $context = null): string
    {
        $locale = $context ? $this->getLocale($context->getContext()) : 'en-GB';
        
        // Load the translation file based on locale
        $translations = $this->loadTranslations($locale);
        
        // Get the translation or fallback
        $translation = $this->getTranslationFromArray($key, $translations);
        
        // Replace parameters if any
        if (!empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $translation = str_replace('{' . $key . '}', $value, $translation);
            }
        }
        
        return $translation;
    }

    private function loadTranslations(string $locale): array
    {
        $file = __DIR__ . '/../Resources/snippet/' . str_replace('-', '_', $locale) . '/messages.' . $locale . '.json';
        
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }
        
        // Try fallback locale if primary locale file doesn't exist
        $fallbackLocale = $this->getFallbackLocale($locale);
        $fallbackFile = __DIR__ . '/../Resources/snippet/' . str_replace('-', '_', $fallbackLocale) . '/messages.' . $fallbackLocale . '.json';
        
        if (file_exists($fallbackFile)) {
            return json_decode(file_get_contents($fallbackFile), true);
        }
        
        // Return empty array if no translation file is found
        return [];
    }

    private function getTranslationFromArray(string $key, array $translations): string
    {
        $keys = explode('.', $key);
        $current = $translations;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                return $key;
            }
            $current = $current[$k];
        }
        
        return $current;
    }
}