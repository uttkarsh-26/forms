<?php

namespace Drupal\amapceo_radar\Step;

/**
 * Class StepsEnum.
 */
abstract class StepsEnum {


  /**
   * Total number of steps for classification forms.
   */
  const CLASSIFICATION_DISPUTE_STEPS = 7;

  /**
   * Total number of steps for non classification forms.
   */
  const NON_CLASSIFICATION_DISPUTE_STEPS = 4;

  /**
   * Total number of steps for manage classification forms.
   */
  const MANAGE_CLASSIFICATION_DISPUTE_STEPS = 4;

  /**
   * Steps used in dispute forms.
   */
  const STEP_ONE = 1;
  const STEP_TWO = 2;
  const STEP_THREE = 3;
  const STEP_FOUR = 4;
  const STEP_FIVE = 5;
  const STEP_SIX = 6;
  const STEP_SEVEN = 7;
  const STEP_FINALIZE = 8;


  /**
   * Means no step field is filled yet.
   */
  const STEP_STATE_EMPTY = 1;
  /**
   * Means more than one field is filled but not all.
   */
  const STEP_STATE_INCOMPLETE = 2;
  /**
   * Means all required fields are filled and validated.
   */
  const STEP_STATE_COMPLETE = 3;

  /**
   * Classification States Map.
   */
  const CLASSIFICATION_STATE_MAP = [
    1 => 'Member Information',
    2 => 'Dispute Form',
    3 => 'Manager',
    4 => 'Workplace',
    5 => 'Classification Details',
    6 => 'Classification Summary',
    7 => 'Other Topics',
    8 => 'Confirm'
  ];

  /**
   * Non Classification States Map.
   */
  const NON_CLASSIFICATION_STATE_MAP = [
    1 => 'Member Information',
    2 => 'Workplace',
    3 => 'Issue Summary',
    4 => 'Dates & Documents',
  ];

  /**
   * Manage Classification States Map.
   */
  const MANAGE_CLASSIFICATION_STATE_MAP = [
    1 => 'Member Information',
    2 => 'Dispute Overview',
    3 => 'Informal Details',
    4 => 'Step 1',
  ];

  /**
   * Map steps to it's class.
   *
   * @param int $step
   *   Step number.
   *
   * @return bool
   *   Return true if exist.
   */
  public static function map($step, $form_type) {
    $map = [
      self::STEP_ONE => "Drupal\\amapceo_radar\\Step\\$form_type\\StepOne",
      self::STEP_TWO => "Drupal\\amapceo_radar\\Step\\$form_type\\StepTwo",
      self::STEP_THREE => "Drupal\\amapceo_radar\\Step\\$form_type\\StepThree",
      self::STEP_FOUR => "Drupal\\amapceo_radar\\Step\\$form_type\\StepFour",
      self::STEP_FIVE => "Drupal\\amapceo_radar\\Step\\$form_type\\StepFive",
      self::STEP_SIX => "Drupal\\amapceo_radar\\Step\\$form_type\\StepSix",
      self::STEP_SEVEN => "Drupal\\amapceo_radar\\Step\\$form_type\\StepSeven",
      self::STEP_FINALIZE => "Drupal\\amapceo_radar\\Step\\$form_type\\StepFinalize",
    ];

    return isset($map[$step]) ? $map[$step] : FALSE;
  }

}
