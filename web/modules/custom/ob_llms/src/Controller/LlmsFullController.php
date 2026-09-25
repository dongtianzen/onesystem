<?php

namespace Drupal\ob_llms\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\ob_llms\HtmlToMarkdown;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Serves /llms-full.txt from the entities listed in ob_llms.settings.
 */
class LlmsFullController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->configFactory = $container->get('config.factory');
    return $instance;
  }

  /**
   * Builds the Markdown response.
   */
  public function build(): CacheableResponse {
    // Read without language overrides: the document holds both languages
    // and must not vary by interface language.
    $config = $this->configFactory->getEditable('ob_llms.settings');
    $baseUrl = rtrim((string) $config->get('base_url'), '/');
    $bodyFields = $config->get('body_fields') ?? [];
    $converter = new HtmlToMarkdown($baseUrl);

    // Tag invalidation is the only expiry: every listed entity and the
    // settings are dependencies.
    $cacheability = (new CacheableMetadata())
      ->addCacheableDependency($config)
      ->setCacheMaxAge(Cache::PERMANENT);

    $entities = [];
    foreach ($config->get('items') ?? [] as $item) {
      $entity = $this->entityTypeManager()
        ->getStorage($item['entity_type'])
        ->load($item['id']);
      if ($entity instanceof ContentEntityInterface) {
        $cacheability->addCacheableDependency($entity);
        $entities[] = $entity;
      }
    }

    $out = '# ' . $config->get('title') . "\n\n";
    $out .= '> ' . trim((string) $config->get('summary')) . "\n\n";
    if ($notes = trim((string) $config->get('notes'))) {
      $out .= $notes . "\n\n";
    }

    foreach ($config->get('languages') ?? [] as $language) {
      $langcode = $language['langcode'];
      $out .= '# ' . $language['heading'] . "\n\n";

      foreach ($entities as $entity) {
        // No fallback: an entity without this translation is skipped.
        if (!$entity->hasTranslation($langcode)) {
          continue;
        }
        $translation = $entity->getTranslation($langcode);
        if ($translation instanceof EntityPublishedInterface && !$translation->isPublished()) {
          continue;
        }

        $out .= '## ' . $translation->label() . "\n\n";
        $out .= 'URL: ' . $this->canonicalUrl($translation, $baseUrl, $cacheability) . "\n\n";

        $field = $bodyFields[$entity->getEntityTypeId()] ?? NULL;
        if ($field && $translation->hasField($field)) {
          $markdown = $converter->convert($translation->get($field)->value);
          if ($markdown !== '') {
            $out .= $markdown . "\n\n";
          }
        }
      }
    }

    // Belt and braces: no data URI may ever leave this endpoint.
    $out = preg_replace('#data:[a-z]+/[a-z0-9.+-]+(;base64)?,\S*#i', '', $out);

    $response = new CacheableResponse(rtrim($out) . "\n", 200, [
      'Content-Type' => 'text/markdown; charset=utf-8',
    ]);
    $response->addCacheableDependency($cacheability);
    return $response;
  }

  /**
   * Returns the aliased, language-prefixed URL on the configured base URL.
   */
  protected function canonicalUrl(ContentEntityInterface $translation, string $baseUrl, CacheableMetadata $cacheability): string {
    $generated = $translation->toUrl('canonical', [
      'language' => $translation->language(),
    ])->toString(TRUE);
    $cacheability->addCacheableDependency($generated);

    $path = $generated->getGeneratedUrl();
    $basePath = rtrim(base_path(), '/');
    if ($basePath !== '' && str_starts_with($path, $basePath)) {
      $path = substr($path, strlen($basePath));
    }
    return $baseUrl . '/' . ltrim($path, '/');
  }

}
