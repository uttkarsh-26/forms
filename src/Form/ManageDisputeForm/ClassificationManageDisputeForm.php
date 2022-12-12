<?php

namespace Drupal\amapceo_radar\Form\ManageDisputeForm;

use Drupal\amapceo_radar\Manager\StepManager;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\encrypt\EncryptService;
use Drupal\encrypt\Entity\EncryptionProfile;
use Drupal\webform\Utility\WebformElementHelper;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class manage dispute forms.
 */
class ClassificationManageDisputeForm extends FormBase {

  /**
   * Step Id.
   */
  protected $stepId;

  /**
   * Multi steps of the form.
   *
   * @var \Drupal\amapceo_radar\Step\StepInterface
   */
  protected $step;

  /**
   * Step manager instance.
   */
  protected $stepManager;

  /**
   * Key Value service.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  protected $keyValue;

  /**
   * Encryption service.
   *
   * @var \Drupal\encrypt\EncryptService
   */
  protected $encryption;

  /**
   * Values from drafts.
   *
   * @var array
   */
  protected $draftValues = NULL;

  protected $formType = 'ManageClassification';
  protected $formTypeToManage = 'Classification';


  /**
   * {@inheritdoc}
   */
  public function __construct(KeyValueFactoryInterface $key_value, EncryptService $encryption) {
    $this->stepId = StepsEnum::STEP_ONE;
    $this->stepManager = new StepManager($this->formType);
    $this->submitDisputeStepManager = new StepManager($this->formTypeToManage);
    $this->userId = $this->currentUser()->id();
    $this->keyValue = $key_value;
    $this->encryption = $encryption;
    $this->encryptionProfile = EncryptionProfile::load('dispute_form_draft_submission');
    $this->draftStorage = $this->keyValue->get('dispute_form_draft');
    // Get draft values from the db if any.
    $ecrypted_string = $this->draftStorage->get($this->userId);
    try {
      $decrypted_string = $this->encryption->decrypt($ecrypted_string, $this->encryptionProfile);
    }
    catch (Exception $e) {
      \Drupal::logger('amapceo_radar')->notice($e->getMessage());
    }
    $this->draftValues = unserialize($decrypted_string);
    if (is_array($this->draftValues)) {
      $this->stepId = $this->draftValues['last_active_step'] ?? $this->stepId;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('keyvalue'),
      $container->get('encryption')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'classification_manage_dispute_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attributes']['id'] = 'classification-manage-dispute-form-wrapper';

    $form['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'form-wrapper',
      ],
    ];

    // Get step from step manager.
    $this->step = $this->stepManager->getStep($this->stepId, $this->draftValues);
    // Add step to manager.
    $this->stepManager->addStep($this->step);

    // Attach toolbar.
    $form['wrapper'] += $this->buildToolbarElement();
    // Attach step form elements.
    $form['wrapper'] += $this->step->buildStepFormElements($form_state);

    // Attach buttons.
    $form['wrapper']['actions']['#type'] = 'actions';

    // Progress bar buttons.
    for ($i = 1; $i <= StepsEnum::MANAGE_CLASSIFICATION_DISPUTE_STEPS; $i++) {
      $step = $this->stepManager->getStep($i, $this->draftValues);
      $form['wrapper']['actions']['step'][$i] = [
        '#type' => 'submit',
        '#value' => StepsEnum::MANAGE_CLASSIFICATION_STATE_MAP[$i],
        '#skip_validation' => TRUE,
        '#name' => "step-$i",
        '#goto_step' => $i,
        '#ajax' => [
          'callback' => [$this, 'loadStep'],
          'wrapper' => 'form-wrapper',
          'effect' => 'fade',
        ],
        '#step-state' => $step->getStepState(),
      ];
      // Add step to manager.
      $this->stepManager->addStep($step);
    }

    $form['wrapper']['#progress_bar_details'] = [
      'total_steps' => StepsEnum::MANAGE_CLASSIFICATION_DISPUTE_STEPS,
      'step_id' => $this->stepId,
    ];

    // Pass the step id to the controller.
    $form['step_id'] = [
      '#type' => 'hidden',
      '#value' => $this->stepId,
    ];
    return $form;

  }

  /**
   * Ajax callback to load new step.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state interface.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function loadStep(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $messages = $this->messenger()->all();
    $this->messenger()->deleteAll();
    if (!empty($messages)) {
      // Form did not validate, get messages and render them.
      $messages = [
        '#theme' => 'status_messages',
        '#message_list' => $messages,
        '#status_headings' => [
          'status' => $this->t('Status message'),
          'error' => $this->t('Error message'),
          'warning' => $this->t('Warning message'),
        ],
      ];
      $response->addCommand(new HtmlCommand('#messages-wrapper', $messages));
    }
    else {
      // Remove messages.
      $response->addCommand(new HtmlCommand('#messages-wrapper', ''));
    }

    // Update Form.
    $response->addCommand(new ReplaceCommand('#classification-manage-dispute-form-wrapper', $form));

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save filled values to step. So we can use them as default_value later on.
    $values = [];
    foreach ($this->step->getFieldNames() as $name) {
      $value_key = $name;
      if (is_array($name)) {
        $name_key = end($name);
        $values[$name_key] = $form_state->getValue($value_key);
      }
      else {
        $values[$name] = $form_state->getValue($value_key);
      }
    }
    // Set values to the step.
    $this->step->setValues($values);

    if ($this->step->getStepState() !== StepsEnum::STEP_STATE_COMPLETE) {
      $this->step->setStepState(StepsEnum::STEP_STATE_INCOMPLETE);
    }

    // Set step to navigate to.
    $triggering_element = $form_state->getTriggeringElement();
    if (!empty($triggering_element['#goto_step'])) {
      $this->stepId = $triggering_element['#goto_step'];
    }

    // If an extra submit handler is set, execute it.
    // We already tested if it is callable before.
    if (isset($triggering_element['#submit_handler'])) {
      $this->{$triggering_element['#submit_handler']}($form, $form_state);
    }

    $form_state->setRebuild(TRUE);
  }

  /**
   * Submit handler for last step of form, final submission should happen here.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state interface.
   */
  public function submitValues(array &$form, FormStateInterface $form_state) {
    // $steps = $this->stepManager->getAllSteps();
    // foreach ($steps as $step) {
    //   $values[] = $step->getValues();
    // }
  }

  protected function buildToolbarElement() {
    $form = [];
    $form['toolbar'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Dispute Tool Bar'),
    ];
    $form['toolbar']['dispute_status'] = [
      '#type' => 'item',
      '#title' => $this->t("Status"),
      '#markup' => $this->t("Status"),
    ];
    $form['toolbar']['sub_status'] = [
      '#type' => 'item',
      '#title' => $this->t("Sub Status"),
      '#markup' => $this->t("Sub Status"),
    ];
    $form['toolbar']['id'] = [
      '#type' => 'item',
      '#title' => $this->t("Dispute (File #)"),
      '#markup' => $this->t("#9999"),
    ];
    $form['toolbar']['id'] = [
      '#type' => 'item',
      '#title' => $this->t("Dispute (File #)"),
      '#markup' => $this->t("#9999"),
    ];
    $form['toolbar']['intake_date'] = [
      '#type' => 'item',
      '#title' => $this->t("Date Submitted"),
      '#markup' => date('D d M Y'),
    ];
    $form['toolbar']['dispute_type'] = [
      '#type' => 'item',
      '#title' => $this->t("Dispute Type"),
      '#markup' => "Classification",
    ];
    $form['toolbar']['dispute_issue'] = [
      '#type' => 'item',
      '#title' => $this->t("Dispute Issue"),
      '#markup' => "Classification",
    ];
    $form['toolbar']['individual_group'] = [
      '#type' => 'item',
      '#title' => $this->t("Group/Individual"),
      '#markup' => "Group",
    ];
    return $form;
  }

}
