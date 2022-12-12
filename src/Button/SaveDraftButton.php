<?php

namespace Drupal\amapceo_radar\Button;

/**
 * Class SaveDraftButton.
 */
class SaveDraftButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'save_draft';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => $this->t('Save Draft'),
      '#submit_handler' => 'saveDraft',
      '#skip_validation' => TRUE,
      '#name' => 'save_draft',
    ];
  }

}
