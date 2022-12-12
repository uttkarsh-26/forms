<?php

namespace Drupal\amapceo_radar\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a 'amapceo_radar_composite'.
 *
 * Cmposites contain a group of sub-elements.
 *
 * @FormElement("amapceo_radar_contact_composite")
 *
 */
class AmapceoRadarContactComposite extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo();// + ['#theme' => 'webform_example_composite'];
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [];
    $elements['contact_date'] = [
      '#type' => 'date',
      '#title' => t('Contact Date'),
      '#required' => TRUE,
      // Use #after_build to add #states.
      //'#after_build' => [[get_called_class(), 'afterBuild']],
    ];
    $elements['contact_note'] = [
      '#type' => 'textarea',
      '#title' => t('Contact Note'),
      '#required' => TRUE,
    ];
    return $elements;
  }

  /**
   * Performs the after_build callback.
   */
  public static function afterBuild(array $element, FormStateInterface $form_state) {
    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['disabled'] = [
      [':input[name="' . $composite_name . '[first_name]"]' => ['empty' => TRUE]],
      [':input[name="' . $composite_name . '[last_name]"]' => ['empty' => TRUE]],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

}
