<?php

namespace Drupal\amapceo_radar\Button;

/**
 * Class StepThreeRemoveOneContactButton.
 */
class StepThreeRemoveOneContactButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function __construct(int $index) {
    $this->index = $index;
  }

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'remove_contact';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Remove Contact'),
      '#submit' => [
        '::removeOneContact',
      ],
      '#ajax' => [
        'callback' => ['\Drupal\amapceo_radar\Step\Classification\StepThree', 'addmoreCallback'],
        'wrapper' => 'contact-record-fieldset-wrapper',
      ],
      '#limit_validation_errors' => [],
      '#skip_validation' => TRUE,
      '#name' => 'index-' . $this->index,
    ];
  }

}
