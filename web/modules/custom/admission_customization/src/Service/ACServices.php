<?php

namespace Drupal\admission_customization\Service;

use Drupal;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\Markup;
use Drupal\taxonomy\Entity\Term;

/**
 * Service that handles incidents data.
 */
class ACServices {

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
   * {@inheritdoc}
   */
  public function getStreamListWithSeats($vid) {
    $query = $this->entityTypeManager->getStorage('taxonomy_term')->getQuery();
    $query->condition('vid', $vid);
    $query->sort('field_code');
    $query->accessCheck(FALSE);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);
    foreach ($terms as $term) {
      $tid_list[$term->id()]['name'] = $term->name->value;
      $tid_list[$term->id()]['seat'] = $term->field_seats->value;
    }

    return $tid_list;
  }

  /*
   * Get Occupied seats as per subject.
   */
  public function getSeatOccupied($type, $field, $stream_id, $session_id) {
    $stream = $this->entityTypeManager->getStorage('taxonomy_term')->load($stream_id);
    $seats = $stream->field_seats->value;
    
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->condition('type', $type)
      ->condition($field, $stream_id)
      ->condition('field_session', $session_id)
      ->condition('field_payment_received', '1')
      ->condition('status', 1);
    $query->accessCheck(FALSE);
    return $query->count()->execute();   
  }

  /*
   * Get Admission status.
   */
  public function admissionStatus($session_id) {

    $vocab = [];
    $vocab['field_stream'] = $this->getStreamListWithSeats('stream');
    $rows = [];

    foreach($vocab as $field => $stream){
      foreach($stream as $key => $value){
        // Total status
        $seats = $value['seat'];
        $occupied = $this->getSeatOccupied('admission_part_1', $field, $key, $session_id);
        $available = $seats - $occupied;
        
        $row = [];
        $row['stream'] = $value['name'];
        $row['seats'] = $seats;
        $row['occupied'] = $occupied;
        $row['availability'] = $available;

        $rows[] = $row;
      }
    }
    
    return $rows;
  }

  /*
   * Get Registration status.
   */
  public function registrationStatus($session_id) {

    $vocab = [];
    $vocab['field_stream'] = $this->getStreamListWithSeats('stream');
    $rows = [];

    foreach($vocab as $field => $stream){
      foreach($stream as $key => $value){
        // Total status
        $admitted = $this->getSeatOccupied('admission_part_1', $field, $key, $session_id);
        $occupied = $this->getSeatOccupied('registration', $field, $key, $session_id);
        $available = $admitted - $occupied;
        
        $row = [];
        $row['stream'] = $value['name'];
        $row['seats'] = $admitted;
        $row['occupied'] = $occupied;
        $row['availability'] = $available;

        $rows[] = $row;
      }
    }
    
    return $rows;
  }

  /**
   * {@inheritdoc}
   * Exist node redirect by User id and Node type.
   */
  public function existNodeRedirectByUidType($uid, $type) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', $type);
    $query->condition('uid', $uid);
    $query->accessCheck(FALSE);
    $nid = $query->execute();
    
    if($nid == !null){
      $nid = current($nid);
      $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$nid);
      $response = new RedirectResponse($alias);
      $response->send();
      \Drupal::messenger()->addWarning(t('You have already submitted the form'));
    }

    return 1;
  }

  /**
   * {@inheritdoc}
   * Check node created by user and and type.
   */
  public function checkExistNodeByUidType($uid, $type) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', $type);
    $query->condition('uid', $uid);
    $query->accessCheck(FALSE);
    $nid = $query->execute();
    
    if($nid == !null){
      return $nid;
    }

    return 0;
  }

  /*
   * Generate Form number by type.
   */
  public function generateFormNo($type) {   
    $query = $this->dbConn->select('node__field_form_number', 'fn');
    $query->join('node_field_data', 'nd', 'fn.entity_id = nd.nid');
    $query->condition('nd.type', $type);
    $query->condition('fn.field_form_number_value', date("y"). "%", 'LIKE');
    $query->addExpression('MAX(fn.field_form_number_value)');
    $form_no = $query->execute()->fetchField();
    
    if($form_no){
      $form_no +=1;
    }else{
      $form_no = date("y").'10001';
    }
    
    return $form_no;   
  }

  /*
  * OFSS Ref. Number check
  */
  public function checkOfss($ofss, $session = NULL, $merit = NULL) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'merit_list')
      ->condition('field_application_no', $ofss);
    
    if($session) {
      $query->condition('field_session', $session);
    }
    if($merit) {
      $query->condition('field_merit_list', $merit);
    }
    
    $query->accessCheck(TRUE);
    $results = $query->execute();
    
    return $results;
  }

}
