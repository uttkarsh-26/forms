<?php

namespace Drupal\amapceo_radar\Step;

use Drupal\amapceo_radar\Manager\StepManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class BaseStep.
 */
abstract class BaseStep implements StepInterface {

  use StringTranslationTrait;
  /**
   * Multi steps of the form.
   *
   * @var StepInterface
   */
  protected $step;

  /**
   * Values of element.
   *
   * @var array
   */
  protected $values;

  /**
   * State of this step. Defaults to STEP_STATE_EMPTY
   *
   * @var int
   */
  protected $stepState = StepsEnum::STEP_STATE_EMPTY;

  /**
   * PrivateTempStore service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $tempstore;

  /**
   * BaseStep constructor.
   */
  public function __construct() {
    $this->step = $this->setStep();
    $this->tempstore = \Drupal::service('tempstore.private')->get('amapceo_radar');
  }

  /**
   * {@inheritdoc}
   */
  public function getStep() {
    return $this->step;
  }

  /**
   * {@inheritdoc}
   */
  public function isLastStep() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setValues($values) {
    $this->values = $values;
    // Once we are editing the form again, let's respect current changes over
    // drafts.
    unset($this->draftValues[$this->step]);
  }

  /**
   * {@inheritdoc}
   */
  public function setValuesFromDraft($values = NULL) {
    $this->draftValues = $values;
  }
  /**
   * {@inheritdoc}
   */
  public function setStepStatesFromDraft($state = NULL) {
    $this->draftStepState = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function getValues() {
    if (!empty($this->draftValues[$this->step])) {
      $this->values = $this->draftValues[$this->step];
    }
    return $this->values;
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

  /**
   * {@inheritdoc}
   */
  public function getStepState() {
    if (!empty($this->draftStepState[$this->step])) {
      $this->stepState = $this->draftStepState[$this->step];
    }
    return $this->stepState;
  }

    /**
   * {@inheritdoc}
   */
  public function setStepState($state) {
    $this->stepState = $state;
    // Once we are editing the form again, let's respect current changes over
    // drafts.
    unset($this->draftStepState[$this->step]);
  }

  /**
   * {@inheritdoc}
   */
  abstract protected function setStep();

}
