<?php

namespace Drupal\amapceo_radar\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides entry level form.
 */
class EntryLevelFormController extends ControllerBase {

  /**
   *  User role constants.
   */
  const MEMBER_ROLE = 'member';
  const WPR_ROLE = 'work_place_representative';

  /**
   * Renders the relevant form.
   */
  public function renderform() {
    $roles = $this->currentUser()->getRoles(TRUE);
    $theme = '';
    $tempstore = \Drupal::service('tempstore.private')->get('amapceo_radar');
    $form_string = '';
    foreach ($roles as $role) {
      if ($role === self::MEMBER_ROLE) {
        $tempstore->set('user_role', $role);
        $form_string = "\Drupal\amapceo_radar\Form\EntryLevelForm\EntryLevelMemberForm";
        $theme = 'entry_level_form_member';
      }
      elseif ($role === self::WPR_ROLE) {
        $tempstore->set('user_role', $role);
        $form_string = "\Drupal\amapceo_radar\Form\EntryLevelForm\EntryLevelWprForm";
        $theme = 'entry_level_form_wpr';
        break;
      }
    }
    $form = $this->formBuilder()->getForm($form_string);
    $form_render = [
      '#theme' => $theme,
      '#form' => $form,
    ];
    return $form_render;

  }
}
