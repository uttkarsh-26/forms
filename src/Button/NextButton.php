<?php

namespace Drupal\amapceo_radar\Button;


/**
 * Class NextButton.
 */
class NextButton extends BaseButton {

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
    return 'next';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
      '#goto_step' => $this->step,
    ];
  }

}
