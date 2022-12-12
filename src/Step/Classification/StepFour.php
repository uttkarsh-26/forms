<?php

namespace Drupal\amapceo_radar\Step\Classification;

use Drupal\amapceo_radar\Button\NextButton;
use Drupal\amapceo_radar\Button\PreviousButton;
use Drupal\amapceo_radar\Button\SkipButton;
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
      new SkipButton(StepsEnum::STEP_FIVE),
      new PreviousButton(StepsEnum::STEP_THREE),
      new NextButton(StepsEnum::STEP_FIVE),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {
    $form['workplace_information_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Workplace Information'),
    ];

    $form['workplace_information_fieldset']['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Department"),
      '#default_value' => $this->getValues()['department'] ?? "",
    ];
    $form['workplace_information_fieldset']['position'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Your Position Title"),
      '#default_value' => $this->getValues()['position'] ?? "",
    ];
    $form['workplace_information_fieldset']['manager'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Manager"),
      '#default_value' => $this->getValues()['manager'] ?? "",
    ];
    // Text bubbdle to be added.
    $form['workplace_information_fieldset']['hra_contact'] = [
      '#type' => 'textfield',
      '#title' => $this->t("HRA Contact"),
      '#default_value' => $this->getValues()['hra_contact'] ?? "",
    ];

    // Only show this field if this is a Group type dispute.
    $dispute_type = $this->tempstore->get('individual_group');
    if ($dispute_type === 'Group') {
      $form['workplace_information_fieldset']['group_members'] = [
        '#type' => 'textarea',
        '#title' => $this->t("Group Members"),
        '#default_value' => $this->getValues()['group_members'] ?? "",
        '#description' => $this->t('(Full name of each member, comma separated)'),
      ];
    }


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'department',
      'position',
      'manager',
      'hra_contact',
      'group_members'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'position' => [
        new ValidatorRequired("This field is required."),
      ],
      'manager' => [
        new ValidatorRequired("This field is required."),
      ],
      'group_members' => [
        new ValidatorRequired("This field is required."),
      ],
    ];
  }

}
