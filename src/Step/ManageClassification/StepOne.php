<?php

namespace Drupal\amapceo_radar\Step\ManageClassification;

use Drupal\amapceo_radar\Manager\StepManager;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class StepOne for Member information.
 */
class StepOne extends BaseStep {

  /**
   * Steps to inherit from classification form.
   */
  const STEPS_TO_INHERIT = [1, 4];

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_ONE;
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
    $form = [];
    $step_manager = new StepManager('Classification');
    foreach (self::STEPS_TO_INHERIT as $step_id) {
      $step = $step_manager->getStep($step_id);
      $form += $step->buildStepFormElements($form_state);
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [];
  }
}
