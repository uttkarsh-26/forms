<?php

namespace Drupal\amapceo_radar\Step\NonClassification;

use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class StepFinalize.
 */
class StepFinalize extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FINALIZE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($form_state) {

    $form['completed'] = [
      '#markup' => $this->t('You have completed the wizard, yeah!'),
    ];

    return $form;
  }

}
