<?php

namespace Drupal\amapceo_radar\Button;

use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class StepThreeAddOneContactButton.
 */
class StepThreeAddOneContactButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'add_contact';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
     return [
      '#type' => 'submit',
      '#value' => $this->t('Add Contact Record'),
      '#submit' => [
        '::addOneContact',
      ],
      '#ajax' => [
        'callback' => ['\Drupal\amapceo_radar\Step\Classification\StepThree', 'addmoreCallback'],
        'wrapper' => 'contact-record-fieldset-wrapper',
      ],
      '#skip_validation' => TRUE,
      '#limit_validation_error' => [],
    ];
  }

}
