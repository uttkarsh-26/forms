<?php

namespace Drupal\amapceo_radar\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a 'amapceo_radar_composite'.
 *
 * Cmposites contain a group of sub-elements.
 *
 * @FormElement("amapceo_radar_additional_document_composite")
 *
 */
class AmapceoRadarAdditonalDocumentComposite extends WebformCompositeBase {

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
    $elements['additional_supporting_files'] = [
      '#title' => t('Additional Supporting Documents File'),
      '#type' => 'managed_file',
      '#title_display' => FALSE,
      '#multiple' => FALSE,
      '#upload_location' => 'private://',
      '#upload_validators' => [
        'file_validate_extensions' => ['doc docx pdf txt'],
        'file_validate_size' => [20 * 1024 * 1024]
      ],
      '#required' => TRUE,
    ];
    $elements['additional_supporting_descriptions'] = [
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#required' => TRUE,
    ];
    return $elements;
  }

}
