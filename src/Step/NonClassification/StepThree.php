<?php

namespace Drupal\amapceo_radar\Step\NonClassification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\SkipButton;
use Drupal\amapceo_radar\Step\Classification\StepSix as ClassificationStepSix;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepThree if we need to override parent Step six.
 */
class StepThree extends ClassificationStepSix {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_THREE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new SkipButton(StepsEnum::STEP_FOUR),
      new PreviousButton(StepsEnum::STEP_TWO),
      new NextButton(StepsEnum::STEP_FOUR),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {
    $form = parent::buildStepFormElements($form_state);
    $form['classification_summary_fieldset']['dispute_overview_mode']['#title'] = $this->t('Please provide an overview of your workplace issue');
    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the contacts in it.
   */
  public static function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['wrapper']['contact_record_fieldset'];
  }

}
