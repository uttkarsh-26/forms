<?php

namespace Drupal\amapceo_radar\Button;

use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class FinishButton.
 */
class FinishButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'finish';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Finish!'),
      '#goto_step' => StepsEnum::STEP_FINALIZE,
      '#submit_handler' => 'submitValues',
      '#name' => 'finish'
    ];
  }

}
