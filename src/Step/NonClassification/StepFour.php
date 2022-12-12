<?php

namespace Drupal\amapceo_radar\Step\NonClassification;

use Drupal\amapceo_radar\Button\AddAdditionalDocContactButton;
use Drupal\amapceo_radar\Button\FinishButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\RemoveOneFileButton;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\amapceo_radar\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepFour.
 */
class StepFour extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FOUR;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new PreviousButton(StepsEnum::STEP_THREE),
      new FinishButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($form_state) {
    $form['#tree'] = TRUE;

    $form['dates_and_documents_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Dates and Documents'),
      '#prefix' => '<div id="dates-and-documents-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['dates_and_documents_fieldset']['incident_date_repetitive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Repetitive/Ongoing?'),
      '#default_value' => $this->getValues()['incident_date_repetitive'] ?? "",
    ];
    $form['dates_and_documents_fieldset']['incident_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Incident Date'),
      '#default_value' => $this->getValues()['incident_date'] ?? '',
    ];
    $form['dates_and_documents_fieldset']['dispute_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Dispute Type'),
      '#options' => [
        '1' => $this->t('Individual'),
        '5' => $this->t('Group'),
      ],
      '#default_value' => $this->getValues()['dispute_type'] ?? "",
    ];
    $form['dates_and_documents_fieldset']['dispute_issue'] = [
      '#type' => 'select',
      '#title' => $this->t('Dispute Issue'),
      '#options' => [
        '1'=> $this->t('Accommodation'),
        '17' => $this->t('Alternative Working Arrangements'),
        '2'=> $this->t('Classification'),
        '3'=> $this->t('Compassionate/Health Transfer'),
        '4'=> $this->t('Competition'),
        '5'=> $this->t('Discipline/Discharge'),
        '7'=> $this->t('Discrimination'),
        '22' => $this->t('Harassment'),
        '8'=> $this->t('Health and Safety'),
        '9'=> $this->t('Hours of Work/Overtime'),
        '10' => $this->t('Leave denials'),
        '11' => $this->t('Merit'),
        '13' => $this->t('Performance Development Plan'),
        '12' => $this->t('Personal Harassment/Bullying'),
        '21' => $this->t('Return to Work'),
        '18' => $this->t('Transition Exit Initiative'),
        '20' => $this->t('Other'),
      ],
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $this->getValues()['dispute_issue'] ?? $this->tempstore->get('dispute_type'),
    ];

    $form['dates_and_documents_fieldset']['additional_supporting_documents_title'] = [
      '#title' => $this->t('Additional Supporting Documents (up to 16 max)'),
      '#type' => 'fieldset',
    ];

    $default_value = $this->getValues();
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
      $form['dates_and_documents_fieldset']['additional_supporting_documents'][$i] = [
        '#type' => 'amapceo_radar_additional_document_composite',
        '#multiple' => TRUE,
        '#default_value' => $default_value['additional_supporting_documents'][$i] ?? [],
      ];
      $remove_contact_button = new RemoveOneFileButton($i);
      $form['dates_and_documents_fieldset']['additional_supporting_documents'][$i]['actions'][$remove_contact_button->getKey()] = $remove_contact_button->build();
      $count++;
    }
    if ($count < 16) {
      $add_additional_button = new AddAdditionalDocContactButton();
      $form['dates_and_documents_fieldset']['actions'][$add_additional_button->getKey()] = $add_additional_button->build();
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      ['wrapper', 'dates_and_documents_fieldset', 'incident_date_repetitive'],
      ['wrapper', 'dates_and_documents_fieldset', 'incident_date'],
      ['wrapper', 'dates_and_documents_fieldset', 'dispute_type'],
      ['wrapper', 'dates_and_documents_fieldset', 'dispute_issue'],
      ['wrapper', 'dates_and_documents_fieldset', 'additional_supporting_documents']
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'incident_date' => [
        new ValidatorRequired("This field is required."),
      ],
      'dispute_type' => [
        new ValidatorRequired("This field is required."),
      ],
      'dispute_issue' => [
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
    return $form['wrapper']['dates_and_documents_fieldset'];
  }

}
