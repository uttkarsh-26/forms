<?php

namespace Drupal\amapceo_radar\Validator;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ValidatorEmail.
 */
class ValidatorEmail extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value, FormStateInterface $form_state) {
    // Validate email
    return \Drupal::service('email.validator')->isValid($value);
  }

}
