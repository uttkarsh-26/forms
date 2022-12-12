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
 * Class StepSix.
 */
class StepSix extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_SIX;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new SkipButton(StepsEnum::STEP_SEVEN),
      new PreviousButton(StepsEnum::STEP_FIVE),
      new NextButton(StepsEnum::STEP_SEVEN),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {
    $form['classification_summary_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Summary'),
    ];
    $form['classification_summary_fieldset']['dispute_overview_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Provide a written overview of the member’s classification concern'),
      '#options' => [
        'doc' => $this->t('Upload a Document'),
        'form' => $this->t('Enter in Form'),
      ],
      '#default_value' => $this->getValues()['dispute_overview_mode'] ?? "",
    ];
    $form['classification_summary_fieldset']['dispute_overview_file_container'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          ':input[name="dispute_overview_mode"]' => ['value' => 'doc'],
        ],
      ],
    ];
    $form['classification_summary_fieldset']['dispute_overview_file_container']['dispute_overview_file'] = [
      '#title' => $this->t('Upload Word/PDF version here'),
      '#title_display' => FALSE,
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => 'private://',
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#states' => [
        'required' => [
          ':input[name="dispute_overview_mode"]' => ['value' => 'doc'],
        ],
      ],
      '#default_value' => $this->getValues()['dispute_overview_file'] ?? "",
    ];
    $form['classification_summary_fieldset']['dispute_overview'] = [
      '#type' => 'textarea',
      '#title' => $this->t("Use the area below to enter your comments"),
      '#title_display' => FALSE,
      '#default_value' => $this->getValues()['dispute_overview'] ?? "",
      '#states' => [
        'visible' => [
          ':input[name="dispute_overview_mode"]' => ['value' => 'form'],
        ],
        'required' => [
          ':input[name="dispute_overview_mode"]' => ['value' => 'form'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'dispute_overview_mode',
      'dispute_overview_file',
      'dispute_overview'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'dispute_overview_mode' => [
        new ValidatorRequired("This field is required."),
      ],
      'dispute_overview_file' => [
        new ValidatorDependentRequired("This field is required.", 'dispute_overview_mode', 'doc'),
      ],
      'dispute_overview' => [
        new ValidatorDependentRequired("This field is required.", 'dispute_overview_mode', 'form'),
      ],
    ];
  }

}
