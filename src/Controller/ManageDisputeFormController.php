<?php

namespace Drupal\amapceo_radar\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides manage dispute form.
 */
class ManageDisputeFormController extends ControllerBase {

  /**
   * Renders the manage classification form.
   */
  public function renderform() {
    $form_string = "\Drupal\amapceo_radar\Form\ManageDisputeForm\ClassificationManageDisputeForm";
    $form = $this->formBuilder()->getForm($form_string);
    return $form;
  }
}
