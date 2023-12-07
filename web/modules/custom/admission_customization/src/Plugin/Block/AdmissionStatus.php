<?php

namespace Drupal\admission_customization\Plugin\Block;

use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'AdmissionStatus' Block.
 *
 * @Block(
 *   id = "admission_status",
 *   admin_label = @Translation("Admission Status"),
 *   category = @Translation("Admission Status"),
 * )
 */
class AdmissionStatus extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\admission_customization\Form\StatusForm');

    return $form;
  }
}
