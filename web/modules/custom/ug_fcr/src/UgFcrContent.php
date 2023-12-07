<?php

namespace Drupal\ug_fcr;

use Drupal;
use Drupal\node\NodeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Service that handles Fcr Data.
 */
class UgFcrContent {

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
   * Get nid of fcr admin based on product variation.
   */
  public function get_frc_nid_by_product_variation_id($p_vid) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'fcr_admin')
      ->condition('field_related_fee', $p_vid)
      ->accessCheck(TRUE);
    $results = $query->execute();

    return $results;
  }

}
