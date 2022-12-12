<?php

namespace Drupal\amapceo_radar\Validator;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ValidatorDependentRequired.
 */
class ValidatorDependentRequired extends BaseValidator {

  protected $parentFieldName;
  protected $expectedParentFieldValue;

  /**
   * BaseValidator constructor.
   *
   * @param string $error_message
   *   Error message.
   * @param string $parent_field_name
   *   Parent field name.
   */
  public function __construct($error_message, $parent_field_name, $expected_parent_field_value) {
    parent::__construct($error_message);
    $this->parentFieldName = $parent_field_name;
    $this->expectedParentFieldValue = $expected_parent_field_value;

  }

  /**
   * {@inheritdoc}
   */
  public function validates($value, FormStateInterface $form_state) {
    $parent_field_value = $form_state->getValue($this->parentFieldName);
    if ($parent_field_value !== $this->expectedParentFieldValue) {
      return TRUE;
    }
    return is_array($value) ? !empty(array_filter($value)) : !empty($value);
  }

}
