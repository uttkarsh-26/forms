<?php

namespace Drupal\amapceo_radar\Step\Classification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\SkipButton;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\amapceo_radar\Validator\ValidatorDependentRequired;
use Drupal\amapceo_radar\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepTwo.
 */
class StepTwo extends BaseStep {

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

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {

    $form['filed_dispute_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Have you filed a Step 1 Classification Dispute Form yet?'),
      '#description' => $this->t('We encourage you to submit this online form to your Workplace Rep before formally contacting your employer in writing using the Step 1 Classification Dispute Form.</b>However, if you have already written your employer about this dispute, please enter the date you did so, at right. This will begin the formal process and timeline.')
    ];

    $form['filed_dispute_fieldset']['filed_dispute_form'] = [
      '#type' => 'radios',
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
      '#default_value' => $this->getValues()['filed_dispute_form'] ?? '',
    ];
    $form['filed_dispute_fieldset']['dispute_details'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          ':input[name="filed_dispute_form"]' => ['value' => 'yes'],
        ],
        'enabled' => [
          ':input[name="filed_dispute_form"]' => ['value' => 'yes'],
        ],
      ],
    ];
    $form['filed_dispute_fieldset']['dispute_details']['dispute_form_submitted_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date Submitted'),
      '#default_value' => $this->getValues()['dispute_form_submitted_date'] ?? '',
    ];
    $form['filed_dispute_fieldset']['dispute_details']['dispute_form'] = [
      '#title' => $this->t('Dispute Form'),
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => 'private://radar',
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $this->getValues()['dispute_form'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'filed_dispute_form',
      'dispute_form_submitted_date',
      'dispute_form'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'filed_dispute_form' => [
        new ValidatorRequired("This field is required."),
      ],
      'dispute_form_submitted_date' => [
        new ValidatorDependentRequired("This field is required.", 'filed_dispute_form', 'yes'),
      ],
    ];
  }

}
