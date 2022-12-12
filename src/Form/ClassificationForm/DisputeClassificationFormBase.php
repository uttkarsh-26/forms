<?php

namespace Drupal\amapceo_radar\Form\ClassificationForm;

use Drupal\amapceo_radar\Button\SaveDraftButton;
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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class Classification forms.
 */
abstract class DisputeClassificationFormBase extends FormBase {

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

  /**
   * Undocumented variable.
   *
   * @var string
   */
  protected $formType = 'Classification';

  /**
   * {@inheritdoc}
   */
  public function __construct(KeyValueFactoryInterface $key_value, EncryptService $encryption) {
    $this->stepId = StepsEnum::STEP_ONE;
    $this->stepManager = new StepManager($this->formType);
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
    catch (\Exception $e) {
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
  abstract public function getFormId();

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attributes']['id'] = 'classification-dispute-form-wrapper';

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

    // Attach step form elements.
    $form['wrapper'] += $this->step->buildStepFormElements($form_state);

    // Attach buttons.
    $form['wrapper']['actions']['#type'] = 'actions';
    $buttons = $this->step->getButtons();
    foreach ($buttons as $button) {
      $form['wrapper']['actions'][$button->getKey()] = $button->build();

      if ($button->ajaxify()) {
        // Add ajax to button.
        $form['wrapper']['actions'][$button->getKey()]['#ajax'] = [
          'callback' => [$this, 'loadStep'],
          'wrapper' => 'form-wrapper',
          'effect' => 'fade',
        ];
      }
      $save_draft_button = new SaveDraftButton();
      $form['wrapper']['actions'][$save_draft_button->getKey()] = $save_draft_button->build();

      $callable = [$this, $button->getSubmitHandler()];
      if ($button->getSubmitHandler() && is_callable($callable)) {
        // Attach submit handler to button, so we can execute it later on..
        $form['wrapper']['actions'][$button->getKey()]['#submit_handler'] = $button->getSubmitHandler();
      }
    }

    // Progress bar buttons.
    for ($i = 1; $i <= StepsEnum::CLASSIFICATION_DISPUTE_STEPS; $i++) {
      $step = $this->stepManager->getStep($i, $this->draftValues);
      $form['wrapper']['actions']['step'][$i] = [
        '#type' => 'submit',
        '#value' => StepsEnum::CLASSIFICATION_STATE_MAP[$i],
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
      'total_steps' => StepsEnum::CLASSIFICATION_DISPUTE_STEPS,
      'step_id' => $this->stepId,
    ];

    // Pass the step id to the controller.
    $form['step_id'] = [
      '#type' => 'hidden',
      '#value' => $this->stepId,
    ];

    $form['#attached']['library'][] = 'amapceo_radar/form_utils';
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
    $response->addCommand(new ReplaceCommand('#classification-dispute-form-wrapper', $form));

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $step_has_error = FALSE;
    // Only validate if validation doesn't have to be skipped.
    // For example on "Previous" button.
    if ($fields_validators = $this->step->getFieldsValidators()) {
      // Validate fields.
      foreach ($fields_validators as $field => $validators) {
        $form_element = WebformElementHelper::getElement($form, $field);
        if (!$form_element) {
          continue;
        }
        // Validate all validators for field.
        $field_value = $form_state->getValue($form_element['#parents']);
        foreach ($validators as $validator) {
          if (!$validator->validates($field_value, $form_state)) {
            $step_has_error = TRUE;
            if (empty($triggering_element['#skip_validation'])) {
              $form_state->setError($form_element, $validator->getErrorMessage());
            }
          }
        }
      }
    }
    // If no error, it means this step is filled and validated hence complete.
    if (!$step_has_error) {
      $this->step->setStepState(StepsEnum::STEP_STATE_COMPLETE);
    }
    else {
      $this->step->setStepState(StepsEnum::STEP_STATE_INCOMPLETE);
    }
    // Validate the incomplete/empty steps on click of final submit and prevent
    // submission if more info needs to be filled out.
    if ($triggering_element['#name'] == 'finish') {
      $steps = $this->stepManager->getAllSteps();
      /** @var  \Drupal\amapceo_radar\Step\StepInterface $step */
      foreach ($steps as $step) {
        $state = $step->getStepState();
        if ($state === StepsEnum::STEP_STATE_INCOMPLETE) {
          $this->messenger()->addError("Please complete all required fields in Step {$step->getStep()}");
          $form_state->setRebuild();
        }
        if ($state === StepsEnum::STEP_STATE_EMPTY) {
          $this->messenger()->addError("Please visit all sections once to ensure all required information is provided.");
          $form_state->setRebuild();
        }
      }
    }
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

    // Save data to db on each step automatically.
    $this->saveDraft($form, $form_state);

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
    // For now just clear drafts at the last step for testing.
    $this->draftStorage->delete($this->userId);
  }

  /**
   * Submit handler for the "add one contact" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public static function addOneContact(array &$form, FormStateInterface $form_state) {
    $num_contacts = $form_state->get('num_contacts');
    $form_state->set('num_contacts', ($num_contacts + 1));
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "add one file" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public static function addOneFile(array &$form, FormStateInterface $form_state) {
    $num_files = $form_state->get('num_files');
    $form_state->set('num_files', ($num_files + 1));
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one contact" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeOneContact(array &$form, FormStateInterface $form_state) {
    $removed_fields = $form_state->get('removed_fields');

    $indexToRemove = (int) explode('-', $form_state->getTriggeringElement()['#name'])[1];
    // Remove the fieldset from $form.
    unset($form['wrapper']['contact_record_fieldset'][$indexToRemove]);

    $contact_record_fieldset = $form_state->getValue(['wrapper', 'contact_record_fieldset']);
    unset($contact_record_fieldset[$indexToRemove]);
    $form_state->setValue(['wrapper', 'contact_record_fieldset'], $contact_record_fieldset);

    $removed_fields[] = $indexToRemove;
    $form_state->set('removed_fields', $removed_fields);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one file" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeOneFile(array &$form, FormStateInterface $form_state) {
    $removed_fields = $form_state->get('removed_fields');

    $indexToRemove = (int) explode('-', $form_state->getTriggeringElement()['#name'])[1];
    // Remove the fieldset from $form.
    unset($form['wrapper']['proposed_job_classification_fieldset'][$indexToRemove]);
    $proposed_job_classification_fieldset = $form_state->getValue(['wrapper', 'proposed_job_classification_fieldset']);
    unset($proposed_job_classification_fieldset[$indexToRemove]);
    $form_state->setValue(['wrapper', 'proposed_job_classification_fieldset'], $proposed_job_classification_fieldset);

    // Keep track of removed fields so we can add new fields at the bottom
    // Without this they would be added where a value was removed.
    $removed_fields[] = $indexToRemove;
    $form_state->set('removed_fields', $removed_fields);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "Save draft" button.
   */
  public function saveDraft(array &$form, FormStateInterface $form_state) {
    // If values exist in drafts we retain those.
    $values = is_array($this->draftValues) ? $this->draftValues : [];
    // Get new overriden values.
    $steps = $this->stepManager->getAllSteps() ?? [];
    foreach ($steps as $id => $step) {
      $values['values'][$id] = $step->getValues();
      $values['states'][$id] = $step->getStepState();
    }
    // Save the step from which save draft is clicked.
    $values['last_active_step'] = $this->stepId;
    // Encrypt the values before saving it to the DB.
    $value = $this->encryption->encrypt(serialize($values), $this->encryptionProfile);
    $this->draftStorage->set($this->userId, $value);
    $triggering_element = $form_state->getTriggeringElement();
    if (!empty($triggering_element['#name']) && $triggering_element['#name'] === 'save_draft') {
      $this->messenger()->addMessage('Draft Submissions Saved.');
    }
  }

}
