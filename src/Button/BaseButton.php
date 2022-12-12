<?php

namespace Drupal\amapceo_radar\Button;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class BaseButton.
 */
abstract class BaseButton implements ButtonInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function ajaxify() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubmitHandler() {
    return FALSE;
  }

}
