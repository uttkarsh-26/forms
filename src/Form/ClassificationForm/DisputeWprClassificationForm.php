<?php

namespace Drupal\amapceo_radar\Form\ClassificationForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\encrypt\EncryptService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides multi step dispute form for Wpr.
 *
 */
class DisputeWprClassificationForm extends DisputeClassificationFormBase {

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
    return 'classification_wpr_dispute_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
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
