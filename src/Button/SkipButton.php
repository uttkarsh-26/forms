<?php

namespace Drupal\amapceo_radar\Button;

/**
 * Class SkipButton.
 */
class SkipButton extends BaseButton {

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
    return 'skip';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Skip'),
      '#goto_step' => $this->step,
      '#skip_validation' => TRUE,
    ];
  }

}
