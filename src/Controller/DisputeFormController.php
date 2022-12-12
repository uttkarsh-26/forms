<?php

namespace Drupal\amapceo_radar\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides Dispute form.
 */
class DisputeFormController extends ControllerBase {

  /**
   * Renders the relevant form.
   */
  public function renderform() {
    $tempstore = \Drupal::service('tempstore.private')->get('amapceo_radar');
    $user_role = $tempstore->get('user_role');
    $classification = $tempstore->get('dispute_type');
    if ($classification === 'classification' && $user_role === 'member') {
      $form_string = "\Drupal\amapceo_radar\Form\ClassificationForm\DisputeMemberClassificationForm";
    }
    elseif ($classification === 'non-classification' && $user_role === 'member') {
      $form_string = "\Drupal\amapceo_radar\Form\NonClassificationForm\DisputeMemberNonClassificationForm";
    }
    elseif ($classification === 'classification' && $user_role === 'work_place_representative') {
      $form_string = "\Drupal\amapceo_radar\Form\ClassificationForm\DisputeWprClassificationForm";
    }
    elseif ($classification === 'non-classification' && $user_role === 'work_place_representative') {
      $form_string = "\Drupal\amapceo_radar\Form\NonClassificationForm\DisputeWprNonClassificationForm";
    }
    $form = $this->formBuilder()->getForm($form_string);
    $form_render = [
      '#theme' => 'dispute_form',
      '#form' => $form,
    ];
    return $form_render;

  }
}
