<?php

namespace Drupal\amapceo_radar\Step\NonClassification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\SkipButton;
use Drupal\amapceo_radar\Step\Classification\StepFour as ClassificationStepFour;
use Drupal\amapceo_radar\Step\StepsEnum;

/**
 * Class StepTwo if we need to override parent step four.
 */
class StepTwo extends ClassificationStepFour {

   /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_TWO;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new SkipButton(StepsEnum::STEP_THREE),
      new PreviousButton(StepsEnum::STEP_ONE),
      new NextButton(StepsEnum::STEP_THREE),
    ];
  }
}
