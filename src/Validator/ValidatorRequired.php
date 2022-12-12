<?php

namespace Drupal\amapceo_radar\Validator;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ValidatorRequired.
 */
class ValidatorRequired extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value, FormStateInterface $form_state) {
    return is_array($value) ? !empty(array_filter($value)) : !empty($value);
  }

}
