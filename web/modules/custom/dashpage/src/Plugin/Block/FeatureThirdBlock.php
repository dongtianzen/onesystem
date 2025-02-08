<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Custom Block' block.
 *
 * @Block(
 *   id = "feature_third_block",
 *   admin_label = @Translation("Feature Third Block"),
 *   category = @Translation("Custom")
 * )
 */
class FeatureThirdBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // $build = [
    //   '#theme' => 'custom_block',
    //   '#content' => $this->t('This is a custom block.'),
    // ];


    $markup = '<section class="py-5">
      <div class="container my-lg-7">
        <div class="row">
          <div class="col-12">
            <div class="mb-5 text-center" data-cue="fadeIn" data-show="true" style="animation-name: fadeIn; animation-duration: 600ms; animation-timing-function: ease; animation-delay: 0ms; animation-direction: normal; animation-fill-mode: both;">
              <h2 class="mb-0">More Block theme Features</h2>
            </div>
          </div>

          <div class="row gy-4">
            <div class="col-md-4 col-12" data-cue="slideInLeft" data-show="true" style="animation-name: slideInLeft; animation-duration: 600ms; animation-timing-function: ease; animation-delay: 0ms; animation-direction: normal; animation-fill-mode: both;">
              <div class="d-flex flex-column gap-4">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="26" viewBox="0 0 32 26" fill="none">
                    <!-- SVG content here -->
                  </svg>
                </div>

                <div>
                  <h4 class="mb-2">Built with Bootstrap 5</h4>
                  <p>Block is the powerful front-end solution based on Bootstrap 5 — Powerful, extensible, and feature-packed frontend toolkit.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    ';

    $markup = '<h2 class="mb-0">More Block theme Features</h2>';

    return [
      '#markup' => $markup,
      '#allowed_tags' => ['div', 'h2', 'p'],
    ];

    return $build;
  }
}
