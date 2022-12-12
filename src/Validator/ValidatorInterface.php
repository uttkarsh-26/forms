<?php

namespace Drupal\amapceo_radar\Validator;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface ValidatorInterface.
 */
interface ValidatorInterface {

  /**
   * Returns bool indicating if validation is ok.
   */
  public function validates($value, FormStateInterface $form_state);

  /**
   * Returns error message.
   */
  public function getErrorMessage();

}
