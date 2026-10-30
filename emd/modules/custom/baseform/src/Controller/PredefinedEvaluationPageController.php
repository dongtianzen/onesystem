<?php

namespace Drupal\baseform\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class PredefinedEvaluationPageController.
 */
class PredefinedEvaluationPageController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    $term = \Drupal::entityManager()->getStorage('taxonomy_term')->create(['vid' => 'evaluationform']);
    // $term = $this->entityManager()->getStorage('taxonomy_term')->create(['vid' => 'evaluationform']);
    // $term->field_evaluationform_questionset['und'][0]['target_id'] = 120;

    dpm($term->field_evaluationform_questionset);
    return $this->entityFormBuilder()->getForm($term);


    return [
      '#type' => 'markup',
      '#markup' => $this->t("Implement method: hello with parameter(s): $name"),
    ];
  }

}
