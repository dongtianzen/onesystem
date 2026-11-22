<?php

declare(strict_types=1);

namespace Drupal\adminpage\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Admin overview page: content, FAQ, user and taxonomy shortcuts.
 */
final class DefaultController extends ControllerBase {

  /**
   * Vocabularies shown in the Taxonomy section, in display order.
   */
  private const VOCABULARIES = ['brand', 'device', 'product', 'solution', 'faq_category'];

  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    LanguageManagerInterface $languageManager,
    private readonly EntityRepositoryInterface $entityRepository,
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('entity.repository'),
    );
  }

  /**
   * Builds the admin overview page.
   *
   * @param string $name
   *   Route parameter kept for URL compatibility (/adminpage/table/all); it
   *   does not change the page.
   */
  public function adminpageStandard(string $name = 'all'): array {
    $cache = (new CacheableMetadata())
      ->addCacheContexts(['user.permissions', 'languages:language_interface']);

    $sections = [];
    $definitions = [
      'content' => [
        'title' => $this->t('Content', [], ['context' => 'adminpage section']),
        'links' => [
          [$this->t('Manage content'), 'view.adminpage_of_content.page_1', [], []],
          [$this->t('Add article'), 'node.add', ['node_type' => 'article'], []],
        ],
      ],
      'faq' => [
        'title' => $this->t('FAQ'),
        'links' => [
          [$this->t('Import FAQs'), 'ob_faq.import_bulk', [], []],
          [$this->t('Add FAQ'), 'node.add', ['node_type' => 'faq'], []],
          [$this->t('FAQ list'), 'system.admin_content', [], ['query' => ['type' => 'faq']]],
          [$this->t('FAQ page'), 'view.faq.faq_page', [], []],
        ],
      ],
      'users' => [
        'title' => $this->t('Users', [], ['context' => 'adminpage section']),
        'links' => [
          [$this->t('Manage users'), 'view.adminpage_of_people.page_1', [], []],
          [$this->t('Add user'), 'user.admin_create', [], []],
        ],
      ],
    ];
    foreach ($definitions as $key => $definition) {
      $links = [];
      foreach ($definition['links'] as [$title, $route, $parameters, $options]) {
        $link = $this->link($title, $route, $parameters, $options, 'btn btn-outline-primary', $cache);
        if ($link) {
          $links[] = $link;
        }
      }
      if ($links) {
        $sections[$key] = ['title' => $definition['title'], 'links' => $links];
      }
    }

    $vocabularies = $this->buildVocabularies($cache);

    $build = [
      '#theme' => 'adminpage_overview',
      '#sections' => $sections,
      '#taxonomy_title' => $this->t('Taxonomy'),
      '#vocabularies' => $vocabularies,
      '#empty' => $this->t('You do not have access to any of the links on this page.'),
      '#attached' => ['library' => ['adminpage/overview']],
    ];
    $cache->applyTo($build);
    return $build;
  }

  /**
   * Accordion data for each vocabulary in self::VOCABULARIES.
   */
  private function buildVocabularies(CacheableMetadata $cache): array {
    $vocabulary_storage = $this->entityTypeManager->getStorage('taxonomy_vocabulary');
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $term_type = $this->entityTypeManager->getDefinition('taxonomy_term');
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_INTERFACE)->getId();
    $language = $this->languageManager->getLanguage($langcode);

    $items = [];
    $any_link = FALSE;
    foreach (self::VOCABULARIES as $vid) {
      $cache->addCacheTags($term_type->getBundleListCacheTags($vid));
      $vocabulary = $vocabulary_storage->load($vid);
      if (!$vocabulary) {
        continue;
      }
      $cache->addCacheableDependency($vocabulary);

      $actions = [];
      foreach ([
        [$this->t('Add term'), 'entity.taxonomy_term.add_form', 'btn btn-sm btn-success'],
        [$this->t('Manage'), 'entity.taxonomy_vocabulary.overview_form', 'btn btn-sm btn-outline-secondary'],
      ] as [$title, $route, $class]) {
        $link = $this->link($title, $route, ['taxonomy_vocabulary' => $vid], [], $class, $cache);
        if ($link) {
          $actions[] = $link;
        }
      }

      $terms = [];
      foreach ($term_storage->loadTree($vid, 0, NULL, TRUE) as $term) {
        $cache->addCacheableDependency($term);
        $translation = $this->entityRepository->getTranslationFromContext($term, $langcode);
        $access = $term->access('update', NULL, TRUE);
        $cache->addCacheableDependency($access);
        $edit = NULL;
        if ($access->isAllowed()) {
          $edit = [
            '#type' => 'link',
            '#title' => $this->t('Edit'),
            '#url' => $term->toUrl('edit-form', ['language' => $language]),
            '#attributes' => ['class' => ['adminpage-term-edit']],
          ];
        }
        $terms[] = ['name' => $translation->label(), 'edit' => $edit];
      }

      if ($actions || array_filter(array_column($terms, 'edit'))) {
        $any_link = TRUE;
      }
      $items[] = [
        'id' => 'adminpage-vocabulary-' . str_replace('_', '-', $vid),
        'label' => $vocabulary->label(),
        'count' => count($terms),
        'actions' => $actions,
        'terms' => $terms,
        'multicolumn' => count($terms) > 20,
      ];
    }
    return $any_link ? $items : [];
  }

  /**
   * Returns a link render array, or NULL when the route is missing or denied.
   */
  private function link($title, string $route, array $parameters, array $options, string $class, CacheableMetadata $cache): ?array {
    try {
      $url = Url::fromRoute($route, $parameters, $options);
      $access = $url->access(NULL, TRUE);
    }
    catch (RouteNotFoundException) {
      return NULL;
    }
    $cache->addCacheableDependency($access);
    if (!$access->isAllowed()) {
      return NULL;
    }
    return [
      '#type' => 'link',
      '#title' => $title,
      '#url' => $url,
      '#attributes' => ['class' => explode(' ', $class)],
    ];
  }

}
