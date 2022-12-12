<?php

namespace Drupal\amapceo_radar\Button;


/**
 * Class PreviousButton.
 */
class PreviousButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function __construct(int $step) {
    $this->step = $step;
  }

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'previous';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Previous'),
      '#goto_step' => $this->step,
      '#skip_validation' => TRUE,
    ];
  }

}
