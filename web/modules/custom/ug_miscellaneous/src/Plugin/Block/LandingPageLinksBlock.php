<?php
namespace Drupal\ug_miscellaneous\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
 * Provides a 'Landing Page Links Block' Block.
 *
 * @Block(
 *   id = "landing_page_links_block",
 *   admin_label = @Translation("Landing Page Links Block"),
 *   category = @Translation("Landing Page Links Block"),
 * )
 */
class LandingPageLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $items = [];
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $type = $node->field_select_type->value;
      if (\Drupal::currentUser()->isAuthenticated()) {
        if($type == 'admission_part_1'){
          $items[] = array(
            'text' => 'Go To Form',
            'url' => '/node/add/admission_part_1',
          );
        }elseif($type == 'registration'){
          $items[] = array(
            'text' => 'Go To Form',
            'url' => '/node/add/registration',
          );
        }elseif($type == 'examination_part_1'){
          $items[] = array(
            'text' => 'Go To Form',
            'url' => '/node/add/examination_part_1',
          );
        }elseif($type == 'examination_part_2'){
          $items[] = array(
            'text' => 'Go To Form',
            'url' => '/node/add/examination_part_2',
          );
        }
      } else {
        if($type == 'admission_part_1'){
          $items[] = array(
            'text' => 'Register for Admission',
            'url' => '/user/register',
          );
        }elseif($type == 'registration'){
          $items[] = array(
            'text' => 'Login By Personal Validation',
            'url' => '/login-by-personal-validation',
          );
        }elseif($type == 'examination_part_1'){
          $items[] = array(
            'text' => 'Login By Personal Validation',
            'url' => '/login-by-personal-validation',
          );
        }elseif($type == 'examination_part_2'){
          $items[] = array(
            'text' => 'Login By Personal Validation',
            'url' => '/login-by-personal-validation',
          );
        }elseif($type == 'examination_part_3'){
          $items[] = array(
            'text' => 'Login By Personal Validation',
            'url' => '/login-by-personal-validation',
          );
        }
        $items[] = array(
          'text' => 'Log in',
          'url' => '/user/login',
        );
      }
    }

    return array (
      '#theme' => 'landing_page_links',
      '#items' => $items,
      '#cache' => [
        'max-age' => 0,
      ]
    );
  }
}
