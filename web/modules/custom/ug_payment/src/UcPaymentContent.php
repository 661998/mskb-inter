<?php

namespace Drupal\ug_payment;

use Drupal;
use Drupal\node\NodeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Service that handles incidents data.
 */
class UcPaymentContent {

  /**
   * The entity type manager.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Database Connection Service.
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $dbConn;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    AccountProxy $current_user,
    Connection $db_connection,
    ConfigFactoryInterface $config_factory
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->dbConn = $db_connection;
    $this->configFactory = $config_factory;
  }
  
  /**
   * Get temp product id by Nid
   */
  public function gettempPidbyNid($nid) {
    $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if($node_obj->get('field_stream')->target_id == '6'){
      $pid = '1';
    }elseif($node_obj->get('field_stream')->target_id == '5'){
      $pid = '2';
    }else{
      $pid = '3';
    }
    return $pid;
    
  }

  /**
   * Get product id by Nid
   */
  public function getPidbyNid($nid) {
    $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    $term_obj = $node_obj->field_stream->referencedEntities();
    $term_obj = reset($term_obj);
    $stream = $term_obj->getName();
    $pid = '';

    $query = \Drupal::entityQuery('commerce_product');
    if($node_obj->bundle() == 'admission_part_1'){
      $query->condition('field_select_type', $node_obj->bundle(), '=');
      $query->condition('type', 'default', '=');
      $query->condition('field_board_university', $node_obj->get('field_board_university')->value, '=');
      $query->condition('field_gender', $node_obj->get('field_gender')->value, '=');
      $query->condition('field_caste_category', $node_obj->get('field_caste_category')->value, '=');
    }

    if($node_obj->bundle() == 'registration'){
      $query->condition('type', 'registration', '=');
    }
    $query->condition('field_session', $node_obj->get('field_session')->target_id, '=');
    $query->condition('field_stream', $node_obj->get('field_stream')->target_id, '=');

    // if($node_obj->bundle() != 'admission_part_1'){
    //   $query->condition('field_student_type', $node_obj->get('field_student_type')->value, '=');
    // }
    
    $query->condition('status', 1);
    $query = $query->execute();
    foreach($query as $row){
      $pid = $row;
    }

    return $pid;
  }
  
  /**
   * Get temp amount by pid
   */
  public function gettempAmountbyPid($pid, $nid) {
    $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if($pid == 1){
      if($node_obj->get('field_gender')->value == 'Male'){
        if($node_obj->get('field_caste_category')->value == 'GEN' || $node_obj->get('field_caste_category')->value == 'BC') {
          $variation_id = 1;
        }elseif($node_obj->get('field_caste_category')->value == 'EBC'){
          $variation_id = 10;
        }else{
          $variation_id = 11;
        }
      }else{
        $variation_id = 11;
      }
    }elseif($pid == 2){
      if($node_obj->get('field_gender')->value == 'Male'){
        if($node_obj->get('field_caste_category')->value == 'GEN' || $node_obj->get('field_caste_category')->value == 'BC') {
          $variation_id = 2;
        }elseif($node_obj->get('field_caste_category')->value == 'EBC'){
          $variation_id = 12;
        }else{
          $variation_id = 13;
        }
      }else{
        $variation_id = 13;
      }
    }else{
      if($node_obj->get('field_gender')->value == 'Male'){
        if($node_obj->get('field_caste_category')->value == 'GEN' || $node_obj->get('field_caste_category')->value == 'BC') {
          $variation_id = 3;
        }elseif($node_obj->get('field_caste_category')->value == 'EBC'){
          $variation_id = 14;
        }else{
          $variation_id = 15;
        }
      }else{
        $variation_id = 15;
      }
    }
    $variation_obj = \Drupal\commerce_product\Entity\ProductVariation::load($variation_id);
    $price = (int)$variation_obj->price->number;
    $variation_details = array('variation_id' => $variation_id, 'amount' => $price);

    return $variation_details;
  }

  /**
   * Get amount by pid
   */
  public function getAmountbyPid($pid, $late_fine, $board = NULL) {
    $query = \Drupal::entityQuery('commerce_product_variation');
    $query->condition('product_id', $pid);
    $query->condition('field_late_fine', $late_fine, '=');
    if(!empty($board)) {
      $query->condition('field_board_university', $board, '=');
    }
    $query->condition('status', 1);
    $query = $query->execute();
    $variation_id = $price = '';
    foreach($query as $row){
      $variation_id = $row;
      $variation_obj = \Drupal\commerce_product\Entity\ProductVariation::load($variation_id);
      $price = (int)$variation_obj->price->number;
    }

    $variation_details = array('variation_id' => $variation_id, 'amount' => $price);

    return $variation_details;
  }

   /**
   * Get product id of clc
   */
  public function getPidbyNidperclc() {
    $query = \Drupal::entityQuery('commerce_product');
    $query->condition('type', 'clc', '=');
    $query->condition('status', 1);
    $query = $query->execute();
    foreach($query as $row){
      $pid = $row;
    }

    return $pid;
  }

  /**
   * Get amount by pid of clc
   */
  public function getAmountbyPidperclc($pid, $pay_type) {
    $query = \Drupal::entityQuery('commerce_product_variation');
    $query->condition('product_id', $pid);
    $query->condition('field_pay_type', $pay_type, '=');
    $query->condition('status', 1);
    $query = $query->execute();
    $variation_id = $price = '';
    foreach($query as $row){
      $variation_id = $row;
      $variation_obj = \Drupal\commerce_product\Entity\ProductVariation::load($variation_id);
      $price = (int)$variation_obj->price->number;
    }

    $variation_details = array('variation_id' => $variation_id, 'amount' => $price);

    return $variation_details;
  }

}
