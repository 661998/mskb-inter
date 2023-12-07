<?php
namespace Drupal\ug_miscellaneous\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'Student Dashboard' Block.
 *
 * @Block(
 *   id = "student_dashboard_block",
 *   admin_label = @Translation("Student Dashboard"),
 *   category = @Translation("Student Dashboard"),
 * )
 */
class StudentDashboardBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $items = [];
    $uid = \Drupal::currentUser()->id();
    $query = \Drupal::entityQuery('node')
            ->condition('uid', $uid)
            ->condition('status', 1);
            // ->accessCheck(TRUE);
    $query->accessCheck(FALSE);
    $result = $query->execute();
    
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($result);
    foreach($nodes as $node){
      $node_type = $node->bundle();
      $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node->id());
      if($node_type == 'admission_part_1'){
        $items[] = array(
          'text' => 'Admission Form Details',
          'url' => $alias,
        ); 
      }elseif($node_type == 'registration'){
        $items[] = array(
          'text' => 'Registration Form Details',
          'url' => $alias,
        );
      }
    }
    
    return array (
      '#theme' => 'student_dashboard',
      '#items' => $items,
      '#cache' => [
        'max-age' => 0,
      ]
    );
  }

}