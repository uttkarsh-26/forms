<?php

namespace Drupal\amapceo_radar\Form\ClassificationForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\encrypt\EncryptService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides multi step dispute form for Members.
 *
 */
class DisputeMemberClassificationForm extends DisputeClassificationFormBase {

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
  public function getFormId() {
    return 'classification_member_dispute_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    // Needs to be prefilled from the MS Access DB if not in drafts, if it is
    // empty it means no values exist in drafts.
    foreach (['first_name', 'last_name', 'ministry'] as $name) {
      if (empty($form['wrapper']['member_information_fieldset'][$name]['#default_value'])) {
        // MS Access data goes in here.
        $form['wrapper']['member_information_fieldset'][$name]['#default_value'] = 'PlaceHolder';
        // Disabled for members.
        $form['wrapper']['member_information_fieldset'][$name]['#disabled'] = TRUE;
      }
    }

    $form['#theme'] = 'classification_dispute';
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
