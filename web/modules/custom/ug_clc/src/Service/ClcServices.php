<?php

namespace Drupal\ug_clc\Service;

use Drupal;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Service that handles incidents data.
 */
class ClcServices {

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

  /*
   * Generate Certificate number by type, field and prefix.
   */
  public function generateCertificateNo($type, $field, $prefix) {
    $query = $this->dbConn->select('node__'.$field, 'fn');
    $query->join('node_field_data', 'nd', 'fn.entity_id = nd.nid');
    $query->condition('nd.type', $type);
    $query->condition('fn.'.$field.'_value', $prefix. "%", 'LIKE');
    $query->addExpression('MAX(fn.'.$field.'_value)');
    $result = $query->execute()->fetchField();
    
    if($result){
      $raw = (int) str_replace($prefix,'',$result);
      $c_no = $prefix.sprintf('%05s', ++$raw);
    }else{
      $c_no = $prefix.'00001';
    }
    
    return $c_no;   
  }

}
