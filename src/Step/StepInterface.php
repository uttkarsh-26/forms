<?php

namespace Drupal\amapceo_radar\Step;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface StepInterface.
 */
interface StepInterface {

  /**
   * Gets the step.
   *
   * @returns step;
   */
  public function getStep();

  /**
   * Returns a renderable form array that defines a step.
   */
  public function buildStepFormElements(FormStateInterface $form_state);

  /**
   * Returns buttons on step.
   */
  public function getButtons();

  /**
   * Indicates if step is last step.
   */
  public function isLastStep();

  /**
   * All fields name.
   *
   * @returns array of all field names.
   */
  public function getFieldNames();

  /**
   * All field validators.
   *
   * @returns array of fields with their validation requirements.
   */
  public function getFieldsValidators();

  /**
   * Sets filled out values of step.
   */
  public function setValues($values);

  /**
   * Gets filled out values of step.
   */
  public function getValues();

  /**
   * Sets values for steps based on existing draft.
   */
  public function setValuesFromDraft(array $values = NULL);

  /**
   * Gets values for steps based on existing draft.
   */
  public function getStepState();
  /**
   * Sets values for steps based on existing draft.
   */
  public function setStepState($state);

}
