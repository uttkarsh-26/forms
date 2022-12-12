<?php

namespace Drupal\amapceo_radar\Form\EntryLevelForm;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements Entry Level Member form.
 */
class EntryLevelMemberForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amapceo_radar_entry_level_member_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $tempstore = \Drupal::service('tempstore.private')->get('amapceo_radar');
    $form['contacted_wpr'] = [
      '#type' => 'select',
      '#title' => $this->t('Have you contacted a Workplace Representative?'),
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
      '#required' => TRUE,
      '#default_value' => $tempstore->get('contacted_wpr') ?? '',
    ];
    $form['contact_wpr_message'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please contact a Workplace Representative to assist you in reporting your workplace issue.'),
      '#states' => [
        'visible' => [
          ':input[name="contacted_wpr"]' => ['value' => 'no'],
        ],
      ],
      '#default_value' => $tempstore->get('contact_wpr_message') ?? '',
    ];
    $form['wpr'] = [
      '#type' => 'select',
      '#title' => $this->t('Which Workplace Representative?'),
      '#options' => [
        // Dummy data, to be replaced by dynamic wpr names.
        'wpr1' => $this->t('Wpr One'),
        'wpr2' => $this->t('Wpr Two'),
      ],
      '#required' => TRUE,
      '#states' => [
        'enabled' => [
          ':input[name="contacted_wpr"]' => ['value' => 'yes'],
        ],
      ],
      '#default_value' => $tempstore->get('wpr') ?? '',
    ];
    $form['involve_benefits'] = [
      '#type' => 'select',
      '#title' => $this->t('Does your workplace issue involve Insured benefits (dental, supplementary health/hospital, vision care LTIP, etc), or job security (surplussing or redeployment)?'),
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
      '#required' => TRUE,
      '#states' => [
        'enabled' => [
          ':input[name="wpr"]' => ['!value' => ''],
        ],
      ],
      '#default_value' => $tempstore->get('involve_benefits') ?? '',
    ];
    $form['involve_benefits_message'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please alert your Workplace Representative that you have either an insured benefits or job security (surplussing or redeployment) issue. They will consult appropriate AMAPCEO staff.'),
      '#states' => [
        'visible' => [
          ':input[name="involve_benefits"]' => ['value' => 'yes'],
        ],
      ],
      '#default_value' => $tempstore->get('involve_benefits_message') ?? '',
    ];
    $form['individual_group'] = [
      '#type' => 'select',
      '#title' => $this->t('Does your workplace issue/dispute involve one or more AMAPCEO members other than yourself?'),
      '#options' => [
        'Group' => $this->t('Yes'),
        'Individual' => $this->t('No'),
      ],
      '#required' => TRUE,
      '#states' => [
        'enabled' => [
          ':input[name="involve_benefits"]' => ['value' => 'no'],
        ],
      ],
      '#default_value' => $tempstore->get('individual_group') ?? '',
    ];
    $form['dispute_type'] = [
      '#type' => 'select',
      '#title' => $this->t("Does your workplace issue involve your position's classification?"),
      '#options' => [
        'classification' => $this->t('Yes'),
        'non-classification' => $this->t('No'),
      ],
      '#required' => TRUE,
      '#states' => [
        'enabled' => [
          ':input[name="individual_group"]' => ['!value' => ''],
        ],
      ],
      '#default_value' => $tempstore->get('dispute_type') ?? '',
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
      '#button_type' => 'primary',
      '#states' => [
        'disabled' => [
          ':input[name="individual_group"]' => ['value' => ''],
          'or',
          ':input[name="dispute_type"]' => ['value' => ''],
        ],
      ],
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
