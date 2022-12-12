<?php

namespace Drupal\amapceo_radar\Button;

/**
 * Class RemoveOneFileButton.
 */
class RemoveOneFileButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function __construct(int $index) {
    $this->index = $index;
  }

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'remove_contact';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Remove File'),
      '#submit' => [
        '::removeOneFile',
      ],
      '#ajax' => [
        'callback' => ['\Drupal\amapceo_radar\Step\NonClassification\StepFour', 'addmoreCallback'],
        'wrapper' => 'dates-and-documents-fieldset-wrapper',
      ],
      '#limit_validation_errors' => [],
      '#skip_validation' => TRUE,
      '#name' => 'index-' . $this->index,
    ];
  }

}
