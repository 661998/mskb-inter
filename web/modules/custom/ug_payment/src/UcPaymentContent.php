<?php

namespace Drupal\ug_payment;

use Drupal;
use Drupal\node\NodeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\commerce_product\Entity\ProductVariation;

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
      $query->condition('type', 'default', '=');
    }

    if($node_obj->bundle() == 'registration'){
      $query->condition('type', 'registration', '=');
    }
    
    $query->condition('field_session', $node_obj->get('field_session')->target_id, '=');
    $query->condition('field_stream', $node_obj->get('field_stream')->target_id, '=');
    
    
    $query->condition('status', 1);
    $query->accessCheck(TRUE);
    $query = $query->execute();
    foreach($query as $row){
      $pid = $row;
    }

    return $pid;
  }

  /**
   * Get amount by pid
   */
  public function getAmountbyPid($pid, $late_fine, $board = NULL, $gender = NULL, $caste_category = NULL, $student_type = NULL) {
    $query = \Drupal::entityQuery('commerce_product_variation');
    $query->condition('product_id', $pid);
    $query->condition('field_late_fine', $late_fine, '=');
    if(!empty($board)) {
      $query->condition('field_board_university', $board, '=');
    }
    if(!empty($gender)) {
      $query->condition('field_gender', $gender, '=');
    }
    if(!empty($caste_category)) {
      $query->condition('field_caste_category', $caste_category, '=');
    }
    if(!empty($student_type)) {
      $query->condition('field_student_type', $student_type, '=');
    }
    $query->condition('status', 1);
    $query->accessCheck(TRUE);
    $query = $query->execute();
    $variation_id = $price = '';
    foreach($query as $row){
      $variation_id = $row;
      $variation_obj = ProductVariation::load($variation_id);
      $price = (int)$variation_obj->price->number;
    }

    $variation_details = array('variation_id' => $variation_id, 'amount' => $price);

    return $variation_details;
  }

   /**
   * Get product id of clc
   */
  public function getPidbyNidperclc() {
    $pid = '';
    $query = \Drupal::entityQuery('commerce_product');
    $query->condition('type', 'clc', '=');
    $query->condition('status', 1);
    $query->accessCheck(TRUE);
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
    $query->accessCheck(TRUE);
    $query = $query->execute();
    $variation_id = $price = '';
    foreach($query as $row){
      $variation_id = $row;
      $variation_obj = ProductVariation::load($variation_id);
      $price = (int)$variation_obj->price->number;
    }

    $variation_details = array('variation_id' => $variation_id, 'amount' => $price);

    return $variation_details;
  }

}
