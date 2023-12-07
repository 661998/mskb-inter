<?php
namespace Drupal\ug_miscellaneous\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'Login By Details' Block.
 *
 * @Block(
 *   id = "login_by_details_block",
 *   admin_label = @Translation("Login By Details"),
 *   category = @Translation("Login By Details"),
 * )
 */
class LoginByDetailsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\ug_miscellaneous\Form\PersonalValidationForm');

    return $form;
  }

}