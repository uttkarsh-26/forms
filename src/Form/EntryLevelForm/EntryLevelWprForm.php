<?php

namespace Drupal\amapceo_radar\Form\EntryLevelForm;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class EntryLevelWprForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amapceo_radar_entry_level_wpr_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $tempstore = \Drupal::service('tempstore.private')->get('amapceo_radar');
    $form['type_of_issue_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Type of Issue or Dispute'),
    ];

    $form['type_of_issue_fieldset']['dispute_type'] = [
      '#type' => 'select',
      '#options' => [
        'classification' => $this->t('Classification'),
        'non-classification' => $this->t('Non Classification'),
      ],
      '#required' => TRUE,
      '#default_value' => $tempstore->get('dispute_type') ?? '',
    ];
    $form['type_of_issue_fieldset']['individual_group'] = [
      '#type' => 'radios',
      '#options' => [
        'Individual' => $this->t('Individual'),
        'Group' => $this->t('Group'),
      ],
      '#required' => TRUE,
      '#default_value' => $tempstore->get('individual_group') ?? '',
    ];
    $form['type_of_issue_fieldset']['actions']['#type'] = 'actions';
    $form['type_of_issue_fieldset']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tempstore = \Drupal::service('tempstore.private')->get('amapceo_radar');
    $values = $form_state->getValues();
    foreach ($values as $key => $value) {
      $tempstore->set($key, $value);
    }
    $form_state->setRedirect('amapceo_radar.dispute_form');
  }

}
