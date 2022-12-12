<?php

namespace Drupal\amapceo_radar\Step\Classification;

use Drupal\amapceo_radar\Button\FinishButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\amapceo_radar\Validator\ValidatorRequired;

/**
 * Class StepSeven.
 */
class StepSeven extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_SEVEN;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new FinishButton(),
      new PreviousButton(StepsEnum::STEP_SIX),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($form_state) {
    $form['other_topics_of_concern_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Other Topics of Concern'),
    ];

    $form['other_topics_of_concern_fieldset']['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('Article "15.10 - Classification" is mandatory — because of this, <b>we already have it and you don\'t need to submit this article.</b></br>If your concern about your position classification/level involves other workplace issues (i.e. discrimination, accommodation, working hours, etc.), please select any Articles associated with those issues.</br>To help you with this, you may download the full Collective Agreement.')
    ];

    $form['other_topics_of_concern_fieldset']['articles'] = [
      '#type' => 'select',
      '#title' => $this->t('Articles'),
      '#options' => [
        // Dummy data, to be replaced by dynamic list.
        '15' => $this->t('15.10 - Classification'),
        '2' => $this->t('Two'),
      ],
      '#multiple' => TRUE,
      '#default_value' => $this->getValues()['articles'] ?? "15",
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'articles'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'articles' => [
        new ValidatorRequired("This field is required."),
      ],
    ];
  }

}
