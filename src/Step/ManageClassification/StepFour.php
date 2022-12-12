<?php

namespace Drupal\amapceo_radar\Step\ManageClassification;

use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class StepFour for Member information.
 */
class StepFour extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FOUR;
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
    return [];
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
