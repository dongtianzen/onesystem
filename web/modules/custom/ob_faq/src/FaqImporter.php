<?php

declare(strict_types=1);

namespace Drupal\ob_faq;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Imports FAQ nodes from JSON.
 *
 * Accepts a list of items, {"faqs": [...]}, or a single item object:
 * @code
 * {
 *   "nid": 812,                    // optional: update this FAQ node
 *   "category": "技术",             // optional: faq_category term name or tid
 *   "status": "draft",             // optional: draft (default) | published
 *   "translations": {
 *     "zh-hans": {"question": "...", "answer": "..."},   // required
 *     "en":      {"question": "...", "answer": "..."}
 *   },
 *   "device_terms": ["LiveU LU800"], // optional: device term names or tids
 *   "products": [250]              // optional: article nids; when
 *                                  // device_terms is omitted, their
 *                                  // device terms are added to the FAQ
 * }
 * @endcode
 * Keys starting with "_" (e.g. "_notes") are ignored.
 */
final class FaqImporter {

  private const BUNDLE = 'faq';
  private const CATEGORY_FIELD = 'field_faq_category';
  private const DEVICE_FIELD = 'field_faq_device';
  private const DEVICE_VOCAB = 'device';
  private const STATUSES = ['draft', 'published'];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Reads a JSON file into a list of FAQ items.
   */
  public function parseFile(string $path): array {
    if (!is_file($path) || !is_readable($path)) {
      throw new \InvalidArgumentException("File not readable: $path");
    }
    return $this->parseString((string) file_get_contents($path));
  }

  /**
   * Decodes JSON text into a list of FAQ items.
   */
  public function parseString(string $json): array {
    $json = (string) preg_replace('/^\xEF\xBB\xBF/', '', trim($json));
    try {
      $data = json_decode($json, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $e) {
      throw new \InvalidArgumentException('Invalid JSON: ' . $e->getMessage());
    }
    if (is_array($data) && isset($data['translations'])) {
      $data = [$data];
    }
    if (is_array($data) && isset($data['faqs'])) {
      $data = $data['faqs'];
    }
    if (!is_array($data) || !array_is_list($data)) {
      throw new \InvalidArgumentException('JSON must be one FAQ object, a list of FAQ objects, or {"faqs": [...]}.');
    }
    return $data;
  }

  /**
   * FAQ category names for help text: "中文（English）", in term weight order.
   */
  public function categoryNames(): array {
    $definition = $this->entityFieldManager->getFieldDefinitions('node', self::BUNDLE)[self::CATEGORY_FIELD] ?? NULL;
    $vids = array_keys($definition?->getSetting('handler_settings')['target_bundles'] ?? []);
    if (!$vids) {
      return [];
    }
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('vid', $vids, 'IN')
      ->sort('weight')
      ->sort('name')
      ->execute();
    $names = [];
    foreach ($storage->loadMultiple($ids) as $term) {
      $zh = $term->hasTranslation('zh-hans') ? $term->getTranslation('zh-hans')->label() : $term->label();
      $en = $term->hasTranslation('en') ? $term->getTranslation('en')->label() : '';
      $names[] = ($en !== '' && $en !== $zh) ? "{$zh}（{$en}）" : $zh;
    }
    return $names;
  }

  /**
   * Imports FAQ items.
   *
   * @param array $items
   *   Decoded FAQ items.
   * @param array $options
   *   - preview (bool): validate and report only, save nothing.
   *   - update (bool): update FAQs that already exist (default: skip).
   *   - text-format (string): text format for answers (default basic_html).
   *   - uid (int): author and revision user.
   *
   * @return array
   *   rows: [label, result, faq nid, devices]; warnings: string[]; errors: int.
   */
  public function import(array $items, array $options = []): array {
    $preview = !empty($options['preview']);
    $update = !empty($options['update']);
    $format = (string) ($options['text-format'] ?? 'basic_html');
    $uid = (int) ($options['uid'] ?? 1);
    $default = $this->languageManager->getDefaultLanguage()->getId();

    $rows = [];
    $warnings = [];
    $errors = 0;

    foreach ($items as $index => $item) {
      $label = '#' . ($index + 1);
      try {
        $problems = $this->validate($item, $default);
        if ($problems) {
          throw new \InvalidArgumentException(implode('; ', $problems));
        }
        $question = trim($item['translations'][$default]['question']);
        $label .= ' ' . mb_strimwidth($question, 0, 40, '…');

        $tids = $this->resolveDevices($item, $warnings, $label);
        $node = $this->findExisting($item, $question, $default);

        if ($node && !$update) {
          $result = 'skipped (exists)';
        }
        else {
          $result = $node ? 'updated' : 'created';
          if (!$preview) {
            $node = $this->save($node, $item, $tids, $format, $uid, $warnings, $label);
          }
        }
        $rows[] = [$label, $preview ? "$result (preview)" : $result, $node?->id() ?? '-', $tids === NULL ? '(unchanged)' : implode(',', $tids)];
      }
      catch (\Throwable $e) {
        $errors++;
        $rows[] = [$label, 'ERROR: ' . $e->getMessage(), '-', ''];
      }
    }
    return ['rows' => $rows, 'warnings' => $warnings, 'errors' => $errors];
  }

  /**
   * Validates one item; returns a list of problems (empty = valid).
   */
  public function validate(mixed $item, string $default): array {
    if (!is_array($item)) {
      return ['item is not an object'];
    }
    $problems = [];
    $translations = $item['translations'] ?? NULL;
    if (!is_array($translations) || empty($translations[$default]['question']) || empty($translations[$default]['answer'])) {
      $problems[] = "translations.$default.question and .answer are required";
    }
    if (is_array($translations)) {
      foreach ($translations as $lang => $tr) {
        if (!$this->languageManager->getLanguage((string) $lang)) {
          $problems[] = "unknown language '$lang'";
        }
        elseif (!is_array($tr) || empty($tr['question']) || empty($tr['answer'])) {
          $problems[] = "translations.$lang needs question and answer";
        }
      }
    }
    if (isset($item['status']) && !in_array($item['status'], self::STATUSES, TRUE)) {
      $problems[] = 'status must be draft or published';
    }
    $isId = static fn($v) => is_int($v) || (is_string($v) && ctype_digit($v));
    if (isset($item['nid']) && !$isId($item['nid'])) {
      $problems[] = 'nid must be a node ID';
    }
    if (isset($item['products']) && (!is_array($item['products']) || count(array_filter($item['products'], $isId)) !== count($item['products']))) {
      $problems[] = 'products must be a list of node IDs';
    }
    if (isset($item['device_terms']) && (!is_array($item['device_terms']) || count(array_filter($item['device_terms'], 'is_scalar')) !== count($item['device_terms']))) {
      $problems[] = 'device_terms must be a list of term IDs or names';
    }
    return $problems;
  }

  /**
   * Works out the device term IDs for an item.
   *
   * @return int[]|null
   *   Term IDs, or NULL to leave the field unchanged.
   */
  private function resolveDevices(array $item, array &$warnings, string $label): ?array {
    if (array_key_exists('device_terms', $item)) {
      $tids = [];
      foreach ($item['device_terms'] as $value) {
        if (!is_int($value) && !ctype_digit((string) $value) && $this->countTermsByName((string) $value, self::DEVICE_FIELD) > 1) {
          $warnings[] = "$label: several device terms are named '$value'; the first match was used. Use the term ID instead.";
        }
        if ($tid =$this->resolveTerm($value, self::DEVICE_FIELD)) {
          $tids[] = $tid;
        }
        else {
          $warnings[] = "$label: device term '$value' not found";
        }
      }
      return array_values(array_unique($tids));
    }
    if (!empty($item['products'])) {
      $tids = $this->devicesFromNodes(array_map('intval', $item['products']), $warnings, $label);
      return array_values(array_unique($tids));
    }
    return NULL;
  }

  /**
   * Collects device-vocabulary term IDs referenced by the given nodes.
   */
  private function devicesFromNodes(array $nids, array &$warnings, string $label): array {
    $tids = [];
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    foreach ($nids as $nid) {
      $node = $nodes[$nid] ?? NULL;
      if (!$node instanceof NodeInterface) {
        $warnings[] = "$label: node $nid not found";
        continue;
      }
      $found = [];
      foreach ($node->getFieldDefinitions() as $name => $definition) {
        if ($name === self::DEVICE_FIELD
          || $definition->getType() !== 'entity_reference'
          || $definition->getSetting('target_type') !== 'taxonomy_term') {
          continue;
        }
        $bundles = array_keys($definition->getSetting('handler_settings')['target_bundles'] ?? []);
        if (in_array(self::DEVICE_VOCAB, $bundles, TRUE)) {
          foreach ($node->get($name)->getValue() as $value) {
            $found[] = (int) $value['target_id'];
          }
        }
      }
      if (!$found) {
        $warnings[] = "$label: node $nid has no device term";
      }
      $tids = array_merge($tids, $found);
    }
    return $tids;
  }

  /**
   * Finds an existing FAQ by nid, or by default-language question text.
   */
  private function findExisting(array $item, string $question, string $default): ?NodeInterface {
    $storage = $this->entityTypeManager->getStorage('node');
    if (!empty($item['nid'])) {
      $node = $storage->load((int) $item['nid']);
      if (!$node instanceof NodeInterface || $node->bundle() !== self::BUNDLE) {
        throw new \InvalidArgumentException("nid {$item['nid']} is not a faq node");
      }
      return $node;
    }
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', self::BUNDLE)
      ->condition('title', $question, '=', $default)
      ->range(0, 2)
      ->execute();
    if (count($ids) > 1) {
      throw new \RuntimeException('several faq nodes have this question; set "nid" to choose one');
    }
    $node = $ids ? $storage->load((int) reset($ids)) : NULL;
    return $node instanceof NodeInterface ? $node : NULL;
  }

  /**
   * Creates or updates one FAQ node with its translations.
   */
  private function save(?NodeInterface $node, array $item, ?array $tids, string $format, int $uid, array &$warnings, string $label): NodeInterface {
    $published = ($item['status'] ?? 'draft') === 'published';
    if (!$node) {
      $node = $this->entityTypeManager->getStorage('node')->create([
        'type' => self::BUNDLE,
        'langcode' => $this->languageManager->getDefaultLanguage()->getId(),
        'uid' => $uid,
      ]);
    }

    foreach ($item['translations'] as $lang => $tr) {
      if ($lang === $node->language()->getId()) {
        $target = $node;
      }
      elseif ($node->hasTranslation($lang)) {
        $target = $node->getTranslation($lang);
      }
      else {
        $target = $node->addTranslation($lang);
      }
      $target->set('title', trim($tr['question']));
      $target->set('body', ['value' => $this->toHtml((string) $tr['answer']), 'format' => $format]);
      $published ? $target->setPublished() : $target->setUnpublished();
    }

    // Both reference fields are untranslatable: setting them on the
    // default translation applies to all languages.
    if (!empty($item['category'])) {
      if ($tid = $this->resolveTerm($item['category'], self::CATEGORY_FIELD)) {
        $node->set(self::CATEGORY_FIELD, $tid);
      }
      else {
        $warnings[] = "$label: category '{$item['category']}' not found";
      }
    }
    if ($tids !== NULL) {
      $node->set(self::DEVICE_FIELD, array_map(static fn($tid) => ['target_id' => $tid], $tids));
    }

    $node->setNewRevision(TRUE);
    $node->setRevisionUserId($uid);
    $node->setRevisionCreationTime(time());
    $node->setRevisionLogMessage('Imported from JSON (ob_faq)');
    $node->save();
    return $node;
  }

  /**
   * Resolves a term given as tid or name, within the field's vocabularies.
   */
  private function resolveTerm(int|string $value, string $fieldName): ?int {
    $definition = $this->entityFieldManager->getFieldDefinitions('node', self::BUNDLE)[$fieldName] ?? NULL;
    $vids = array_keys($definition?->getSetting('handler_settings')['target_bundles'] ?? []);
    $termStorage = $this->entityTypeManager->getStorage('taxonomy_term');

    if (is_int($value) || ctype_digit($value)) {
      $term = $termStorage->load((int) $value);
      return ($term && (!$vids || in_array($term->bundle(), $vids, TRUE))) ? (int) $value : NULL;
    }
    $query = $termStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('name', trim($value))
      ->range(0, 1);
    if ($vids) {
      $query->condition('vid', $vids, 'IN');
    }
    $ids = $query->execute();
    return $ids ? (int) reset($ids) : NULL;
  }

  /**
   * Counts terms with this exact name in the field's vocabularies.
   */
  private function countTermsByName(string $name, string $fieldName): int {
    $definition = $this->entityFieldManager->getFieldDefinitions('node', self::BUNDLE)[$fieldName] ?? NULL;
    $vids = array_keys($definition?->getSetting('handler_settings')['target_bundles'] ?? []);
    $query = $this->entityTypeManager->getStorage('taxonomy_term')->getQuery()
      ->accessCheck(FALSE)
      ->condition('name', trim($name));
    if ($vids) {
      $query->condition('vid', $vids, 'IN');
    }
    return (int) $query->count()->execute();
  }

  /**
   * Wraps plain-text answers in <p>; answers with HTML tags are kept.
   */
  private function toHtml(string $answer): string {
    $answer = trim($answer);
    if (preg_match('#</?(p|br|ul|ol|li|table|tr|td|th|strong|b|em|i|a|h[1-6]|div|span|code)\b[^>]*>#i', $answer)) {
      return $answer;
    }
    $paragraphs = preg_split('/\R{2,}/u', $answer) ?: [$answer];
    return implode("\n", array_map(
      static fn($p) => '<p>' . nl2br(Html::escape(trim($p))) . '</p>',
      $paragraphs
    ));
  }

}
