<?php

namespace Drupal\amapceo_radar\Step\Classification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\SkipButton;
use Drupal\amapceo_radar\Step\BaseStep;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\amapceo_radar\Validator\ValidatorEmail;
use Drupal\amapceo_radar\Validator\ValidatorRequired;
/**
 * Class StepOne for Member information.
 */
class StepOne extends BaseStep {

  /**
   * Lists all the step one fields.
   */
  const STEP_ONE_FIELDS = [
    'first_name',
    'last_name',
    'ministry',
    'bargaining_unit',
    'contact_preference',
    'work_email',
    'work_phone',
    'home_email',
    'home_phone',
    'individual_group',
    'wpr'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_ONE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new SkipButton(StepsEnum::STEP_TWO),
      new NextButton(StepsEnum::STEP_TWO),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($form_state) {

    $form['member_information_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Member Information'),
    ];

    $form['member_information_fieldset']['first_name'] = [
      '#type' => 'textfield',
      '#title' => t("First Name"),
      '#default_value' => $this->getValues()['first_name'] ?? "",
    ];
    $form['member_information_fieldset']['last_name'] = [
      '#type' => 'textfield',
      '#title' => t("Last Name"),
      '#default_value' => $this->getValues()['last_name'] ?? "",
    ];
    $form['member_information_fieldset']['ministry'] = [
      '#type' => 'select',
      '#title' => $this->t('Ministry'),
      '#options' => [
        // Dummy data, to be replaced by dynamic wpr names.
        'min1' => $this->t('Ministry One'),
        'min2' => $this->t('Ministry Two'),
      ],
      '#default_value' => $this->getValues()['ministry'] ?? "",
    ];
    $form['member_information_fieldset']['bargaining_unit'] = [
      '#type' => 'textfield',
      '#title' => t("Bargaining Unit"),
      '#default_value' => $this->getValues()['bargaining_unit'] ?? "OPS",
      '#disabled' => TRUE,
    ];
    $form['contact_dispute_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Contact for dispute'),
    ];
    $form['contact_dispute_fieldset']['contact_preference'] = [
      '#type' => 'radios',
      '#title' => $this->t('Please contact me through'),
      '#options' => [
        'home' => $this->t('My Home'),
        'work' => $this->t('My Work'),
      ],
    ];
    if (!empty($this->getValues()['contact_preference'])) {
      $form['contact_dispute_fieldset']['contact_preference']['#default_value'] = $this->getValues()['contact_preference'];
    }
    $form['contact_dispute_fieldset']['work_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Work Email'),
      '#states' => [
        'visible' => [
          ':input[name="contact_preference"]' => ['value' => 'work'],
        ],
      ],
      '#default_value' => $this->getValues()['work_email'] ?? "work@email.com",
    ];
    $form['contact_dispute_fieldset']['work_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Work Phone'),
      '#states' => [
        'visible' => [
          ':input[name="contact_preference"]' => ['value' => 'work'],
        ],
      ],
      '#default_value' => $this->getValues()['work_phone'] ?? "1234567890",
    ];
    $form['contact_dispute_fieldset']['home_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Home Email'),
      '#states' => [
        'visible' => [
          ':input[name="contact_preference"]' => ['value' => 'home'],
        ],
      ],
      '#default_value' => $this->getValues()['home_email'] ?? "home@email.com",
    ];
    $form['contact_dispute_fieldset']['home_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Home Phone'),
      '#states' => [
        'visible' => [
          ':input[name="contact_preference"]' => ['value' => 'home'],
        ],
      ],
      '#default_value' => $this->getValues()['home_phone'] ?? "1234567890",
    ];

    // Save metadata from Entry level forms to be passed to final submission.
    $dispute_type = $this->tempstore->get('dispute_type') ?? '';
    $form['individual_group'] = [
      '#type' => 'hidden',
      '#value' => $this->getValues()['individual_group'] ?? $dispute_type,
    ];
    $wpr = $this->tempstore->get('wpr') ?? '';
    $form['wpr'] = [
      '#type' => 'hidden',
      '#value' => $this->getValues()['wpr'] ?? $wpr,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return self::STEP_ONE_FIELDS;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    $return = array_fill_keys(self::STEP_ONE_FIELDS, [new ValidatorRequired("This is a required field.")]);
    unset($return['wpr']); unset($return['individual_group']);
    $return['work_email'][] = $return['home_email'][] = new ValidatorEmail("Please enter a valid email address");
    return $return;
  }

}
