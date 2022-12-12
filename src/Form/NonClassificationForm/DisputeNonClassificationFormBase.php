<?php

namespace Drupal\amapceo_radar\Form\NonClassificationForm;

use Drupal\amapceo_radar\Button\SaveDraftButton;
use Drupal\amapceo_radar\Form\ClassificationForm\DisputeClassificationFormBase;
use Drupal\amapceo_radar\Step\StepsEnum;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\encrypt\EncryptService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class Classification forms.
 *
 */
abstract class DisputeNonClassificationFormBase extends DisputeClassificationFormBase {

  /**
   * Step Id.
   */
  protected $stepId;

  /**
   * Multi steps of the form.
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
   * Form type.
   *
   * @var string
   */
  protected $formType = 'NonClassification';

  /**
   * Key value storage.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $draftStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(KeyValueFactoryInterface $key_value, EncryptService $encryption) {
    parent::__construct($key_value, $encryption);
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

    $form['#attributes']['id'] = 'non-classification-dispute-form-wrapper';

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

    for ($i = 1; $i <= StepsEnum::NON_CLASSIFICATION_DISPUTE_STEPS; $i++) {
      $step = $this->stepManager->getStep($i, $this->draftValues);
      $form['wrapper']['actions']['step'][$i] = [
        '#type' => 'submit',
        '#value' => StepsEnum::NON_CLASSIFICATION_STATE_MAP[$i],
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
      'total_steps' => StepsEnum::NON_CLASSIFICATION_DISPUTE_STEPS,
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
    $response->addCommand(new ReplaceCommand('#non-classification-dispute-form-wrapper', $form));

    return $response;
  }

}
