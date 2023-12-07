<?php
namespace Drupal\ug_miscellaneous\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'Front Callout' Block.
 *
 * @Block(
 *   id = "front_callout_block",
 *   admin_label = @Translation("Front Callout"),
 *   category = @Translation("Front Callout"),
 * )
 */
class FrontCalloutBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $ugsettingManager = \Drupal::config('ug_settings.settings');
    $ap1 = $ugsettingManager->get('admission_part_1');
    $ap1_session = $ugsettingManager->get('session_list');

    $reg = $ugsettingManager->get('registration');
    $reg_session = $ugsettingManager->get('registration_session');

    $ep1 = $ugsettingManager->get('examination_part_1');
    $ep1_session = $ugsettingManager->get('p1exam_session_list');

    $ep2 = $ugsettingManager->get('examination_part_2');
    $ep2_session = $ugsettingManager->get('p2exam_session_list');

    $items = [];
    if($ap1){
      $items[] = array(
        'text' => 'Inter Admission - '.Term::load($ap1_session)->get('name')->value,
        'url' => '/inter-admission',
      ); 
    }
    
    if($reg){
      $items[] = array(
        'text' => 'Inter Registration - '.Term::load($reg_session)->get('name')->value,
        'url' => '/inter-registration',
      ); 
    } 

    if($ep1){
      $items[] = array(
        'text' => 'UG1 Examination - '.Term::load($ep1_session)->get('name')->value,
        'url' => '/ug1-examination',
      ); 
    }
    
    if($ep2){
      $items[] = array(
        'text' => 'UG2 Admission & Examination - '.Term::load($ep2_session)->get('name')->value,
        'url' => '/ug2-examination',
      ); 
    }

    return array (
      '#theme' => 'front_callout',
      '#items' => $items,
      '#cache' => [
        'max-age' => 0,
      ]
    );
  }

}