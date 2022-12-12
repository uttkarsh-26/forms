<?php

namespace Drupal\amapceo_radar\Validator;

/**
 * Class BaseValidator.
 */
abstract class BaseValidator implements ValidatorInterface {

  protected $errorMessage;

  /**
   * BaseValidator constructor.
   *
   * @param string $error_message
   *   Error message.
   */
  public function __construct($error_message) {
    $this->errorMessage = $error_message;
  }

  /**
   * {@inheritdoc}
   */
  public function getErrorMessage() {
    return $this->errorMessage;
  }

}
