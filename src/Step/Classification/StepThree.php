<?php

namespace Drupal\amapceo_radar\Step\Classification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\SkipButton;
use Drupal\amapceo_radar\Button\StepThreeAddOneContactButton;
use Drupal\amapceo_radar\Button\StepThreeRemoveOneContactButton;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\amapceo_radar\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepThree.
 */
class StepThree extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_THREE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new SkipButton(StepsEnum::STEP_FOUR),
      new PreviousButton(StepsEnum::STEP_TWO),
      new NextButton(StepsEnum::STEP_FOUR),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {
    $default_value = $this->getValues();
    $draft_count = 0;
    if (!empty($default_value['contact_record'])) {
      $draft_count = count($default_value['contact_record']);
    }

    // Gather the number of names in the form already.
    $num_contacts = $form_state->get('num_contacts');
    if ($num_contacts === NULL) {
      $num_contacts = $draft_count;
      $form_state->set('num_contacts', $draft_count);
    }

    // Get a list of fields that were removed.
    $removed_fields = $form_state->get('removed_fields');
    // If no fields have been removed yet we use an empty array.
    if ($removed_fields === NULL) {
      $form_state->set('removed_fields', []);
      $removed_fields = $form_state->get('removed_fields');
    }

    $form['#tree'] = TRUE;

    $form['contact_record_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Have you spoken to your immediate supervisor about your concerns regarding your classification?'),
      '#description' => $this->t('If so, create separate contact records for each communication'),
      '#prefix' => '<div id="contact-record-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];


    $count = 0;
    for ($i = 0; $i < $num_contacts; $i++) {
      // Check if field was removed.
      if (in_array($i, $removed_fields, TRUE)) {
        // Skip if field was removed and move to the next field.
        continue;
      }

      $form['contact_record_fieldset']['contact_record'][$i] = [
        '#type' => 'amapceo_radar_contact_composite',
        '#multiple' => TRUE,
        '#default_value' => $default_value['contact_record'][$i] ?? [],
      ];
      $remove_contact_button = new StepThreeRemoveOneContactButton($i);
      $form['contact_record_fieldset']['contact_record'][$i]['actions'][$remove_contact_button->getKey()] = $remove_contact_button->build();
      $count++;
    }

    // Allow adding upto 3 contacts via Add Contact Record.
    if ($count < 3) {
      $form['contact_record_fieldset']['actions'] = [
        '#type' => 'actions',
      ];
      $add_contact_button = new StepThreeAddOneContactButton();
      $form['contact_record_fieldset']['actions'][$add_contact_button->getKey()] = $add_contact_button->build();
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      ['wrapper', 'contact_record_fieldset', 'contact_record']
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'contact_record' => [
        new ValidatorRequired("This field is required."),
      ],
    ];
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the contacts in it.
   */
  public static function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['wrapper']['contact_record_fieldset'];
  }

}
