<?php

namespace Drupal\amapceo_radar\Step\Classification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\SkipButton;
use Drupal\amapceo_radar\Button\StepFiveAddAdditionalDocContactButton;
use Drupal\amapceo_radar\Button\StepFiveRemoveOneFileButton;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\amapceo_radar\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepFive.
 */
class StepFive extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FIVE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new SkipButton(StepsEnum::STEP_SIX),
      new PreviousButton(StepsEnum::STEP_FOUR),
      new NextButton(StepsEnum::STEP_SIX),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($form_state) {

    $form['#tree'] = TRUE;

    $default_value = $this->getValues();

    $form['current_job_classification_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Current Job Classification Information'),
    ];
    $form['current_job_classification_fieldset']['job_class'] = [
      '#type' => 'textfield',
      '#title' => t("Current Job Class"),
      '#default_value' => $default_value['job_class'] ?? "",
    ];
    $form['current_job_classification_fieldset']['curent_home_position'] = [
      '#type' => 'radios',
      '#title' => $this->t('Is this currently your home position?'),
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
      '#default_value' => $default_value['curent_home_position'] ?? "",
    ];
    $form['current_job_classification_fieldset']['job_description_document'] = [
      '#title' => $this->t('Upload Job Description Document'),
      '#type' => 'managed_file',
      '#upload_location' => 'private://',
      '#multiple' => FALSE,
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $default_value['job_description_document'] ?? "",
    ];
    $form['current_job_classification_fieldset']['rationale_document'] = [
      '#title' => $this->t('Upload Rationale Document'),
      '#type' => 'managed_file',
      '#upload_location' => 'private://',
      '#multiple' => FALSE,
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $default_value['rationale_document'] ?? "",
    ];

    $form['proposed_job_classification_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Proposed Job Classification Information'),
      '#prefix' => '<div id="proposed-job-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['proposed_job_classification_fieldset']['proposed_job_class'] = [
      '#type' => 'textfield',
      '#title' => t("Proposed Job Class"),
      '#default_value' => $default_value['proposed_job_class'] ?? "",
    ];
    $form['proposed_job_classification_fieldset']['proposed_job_description_file'] = [
      '#title' => $this->t('Upload Proposed Job Description Document'),
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => 'private://',
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $default_value['proposed_job_description_file'] ?? "",
    ];
    $form['proposed_job_classification_fieldset']['proposed_rationale_file'] = [
      '#title' => $this->t('Upload Proposed Rationale Document'),
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => 'private://',
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $default_value['proposed_rationale_file'] ?? "",
    ];
    $form['proposed_job_classification_fieldset']['comparator_job_specs_file'] = [
      '#title' => $this->t('Upload Comparator Job Specs'),
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#upload_location' => 'private://',
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $default_value['comparator_job_specs_file'] ?? "",
    ];
    $form['proposed_job_classification_fieldset']['classification_review_form_file'] = [
      '#title' => $this->t('Upload Classification Review Form'),
      '#type' => 'managed_file',
      '#upload_location' => 'private://',
      '#multiple' => FALSE,
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#default_value' => $default_value['classification_review_form_file'] ?? "",
    ];

    $form['proposed_job_classification_fieldset']['actions'] = [
      '#type' => 'actions',
    ];
    $form['proposed_job_classification_fieldset']['additional_supporting_documents_title'] = [
      '#title' => $this->t('Additional Supporting Documents (up to 16 max)'),
      '#type' => 'fieldset',
    ];

    // Gather the number of files in the form already.
    $draft_count = 0;
    if (!empty($default_value['additional_supporting_documents'])) {
      $draft_count = count($default_value['additional_supporting_documents']);
    }
    $num_files = $form_state->get('num_files');
    if ($num_files === NULL) {
      $num_files = $draft_count;
      $form_state->set('num_files', $draft_count);
    }

    // Get a list of fields that were removed.
    $removed_fields = $form_state->get('removed_fields');
    // If no fields have been removed yet we use an empty array.
    if ($removed_fields === NULL) {
      $form_state->set('removed_fields', []);
      $removed_fields = $form_state->get('removed_fields');
    }

    $count = 0;
    for ($i = 0; $i < $num_files; $i++) {
      // Check if field was removed.
      if (in_array($i, $removed_fields, TRUE)) {
        // Skip if field was removed and move to the next field.
        continue;
      }
      $form['proposed_job_classification_fieldset']['additional_supporting_documents'][$i] = [
        '#type' => 'amapceo_radar_additional_document_composite',
        '#multiple' => TRUE,
        '#default_value' => $default_value['additional_supporting_documents'][$i] ?? [],
      ];
      $remove_contact_button = new StepFiveRemoveOneFileButton($i);
      $form['proposed_job_classification_fieldset']['additional_supporting_documents'][$i]['actions'][$remove_contact_button->getKey()] = $remove_contact_button->build();
      $count++;
    }
    if ($count < 16) {
      $add_additional_button = new StepFiveAddAdditionalDocContactButton();
      $form['proposed_job_classification_fieldset']['actions'][$add_additional_button->getKey()] = $add_additional_button->build();
    }


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      ['wrapper', 'current_job_classification_fieldset', 'job_class'],
      ['wrapper', 'current_job_classification_fieldset', 'curent_home_position'],
      ['wrapper', 'current_job_classification_fieldset', 'job_description_document'],
      ['wrapper', 'current_job_classification_fieldset', 'rationale_document'],
      ['wrapper', 'proposed_job_classification_fieldset', 'proposed_job_class'],
      ['wrapper', 'proposed_job_classification_fieldset', 'proposed_rationale_file'],
      ['wrapper', 'proposed_job_classification_fieldset', 'comparator_job_specs_file'],
      ['wrapper', 'proposed_job_classification_fieldset', 'classification_review_form_file'],
      ['wrapper', 'proposed_job_classification_fieldset', 'additional_supporting_documents']
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'job_class' => [
        new ValidatorRequired("This field is required."),
      ],
      'curent_home_position' => [
        new ValidatorRequired("This field is required."),
      ],
      'proposed_job_class' => [
        new ValidatorRequired("This field is required."),
      ],
    ];
  }

  /**
   * Callback for ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the contacts in it.
   */
  public static function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['wrapper']['proposed_job_classification_fieldset'];
  }

}
