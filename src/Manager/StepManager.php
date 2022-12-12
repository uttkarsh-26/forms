<?php

namespace Drupal\amapceo_radar\Manager;

use Drupal\amapceo_radar\Step\StepInterface;
use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class StepManager.
 */
class StepManager {

  /**
   * Multi steps of the form.
   */
  protected $steps;

  /**
   * StepManager constructor.
   */
  public function __construct(string $form_type) {
    $this->formType = $form_type;
  }

  /**
   * Add a step to the steps property.
   */
  public function addStep(StepInterface $step) {
    if (empty($this->steps[$step->getStep()])) {
      $this->steps[$step->getStep()] = $step;
    }
  }

  /**
   * Fetches step from steps property, If it doesn't exist, create step object.
   *
   * @param int $step_id
   *   Step ID.
   *
   * @return \Drupal\amapceo_radar\Step\StepInterface
   *   Return step object.
   */
  public function getStep($step_id, $values = NULL) {
    if (isset($this->steps[$step_id])) {
      // If step was already initialized, use that step.
      // Chance is there are values stored on that step.
      $step = $this->steps[$step_id];
    }
    else {
      // Get class.
      $class = StepsEnum::map($step_id, $this->formType);
      // Init step.
      $step = new $class($this);
      // We do this only once during initialization of the step.
      !empty($values['values']) ? $step->setValuesFromDraft($values['values']) : '';
      !empty($values['states']) ? $step->setStepStatesFromDraft($values['states']) : '';
    }

    return $step;
  }

  /**
   * Get all steps.
   *
   * @return \Drupal\amapceo_radar\Step\StepInterface
   *   Steps.
   */
  public function getAllSteps() {
    return $this->steps;
  }

}
