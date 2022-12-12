<?php

namespace Drupal\amapceo_radar\Button;


/**
 * Class StepFiveAddAdditionalDocContactButton.
 */
class StepFiveAddAdditionalDocContactButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'add_additional_doc';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
     return [
      '#type' => 'submit',
      '#value' => $this->t('Add Another File+'),
      '#submit' => [
        '::addOneFile',
      ],
      '#ajax' => [
        'callback' => ['\Drupal\amapceo_radar\Step\Classification\StepFive', 'addmoreCallback'],
        'wrapper' => 'proposed-job-fieldset-wrapper',
      ],
      '#limit_validation_errors' => [],
      '#skip_validation' => TRUE,
    ];
  }

}
