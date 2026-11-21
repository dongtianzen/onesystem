<?php

declare(strict_types=1);

namespace Drupal\ob_faq\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ob_faq\ProductExporter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Export page and JSON download of product/solution content.
 */
final class ProductExportController extends ControllerBase {

  public function __construct(private readonly ProductExporter $exporter) {}

  public static function create(ContainerInterface $container): static {
    return new static($container->get('ob_faq.product_exporter'));
  }

  /**
   * Page with counts and two download buttons.
   */
  public function page(): array {
    $counts = $this->exporter->counts();
    $button = fn(string $label, string $withDevice, string $class) => Link::fromTextAndUrl(
      $label,
      Url::fromRoute('ob_faq.export_download', [], [
        'query' => ['with_device' => $withDevice],
        'attributes' => ['class' => ['button', $class]],
      ])
    )->toRenderable();

    return [
      'intro' => [
        '#markup' => '<p>' . $this->t('Download the text of published product and solution pages as JSON, for drafting FAQs. The file also lists existing FAQs so drafts can avoid duplicates.') . '</p>',
      ],
      'counts' => [
        '#theme' => 'item_list',
        '#items' => [
          $this->t('Product and solution pages: @n', ['@n' => $counts['pages']]),
          $this->t('Pages with a device term: @n', ['@n' => $counts['with_device']]),
          $this->t('Existing FAQs: @n', ['@n' => $counts['faqs']]),
        ],
      ],
      'actions' => [
        '#type' => 'container',
        'with_device' => $button((string) $this->t('Download pages with a device term'), '1', 'button--primary'),
        'all' => $button((string) $this->t('Download all pages'), '0', 'button--secondary'),
      ],
      '#cache' => ['max-age' => 0],
    ];
  }

  /**
   * JSON file download.
   */
  public function download(Request $request): Response {
    $withDevice = $request->query->get('with_device', '1') !== '0';
    $json = json_encode(
      $this->exporter->export($withDevice),
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );
    $filename = 'oneband-products-' . ($withDevice ? 'device' : 'all') . '-' . date('Ymd-His') . '.json';
    return new Response((string) $json, 200, [
      'Content-Type' => 'application/json; charset=utf-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Cache-Control' => 'no-store, private',
    ]);
  }

}
