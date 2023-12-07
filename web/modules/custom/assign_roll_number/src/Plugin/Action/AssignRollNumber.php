<?php

namespace Drupal\assign_roll_number\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Action description.
 *
 * @Action(
 *   id = "assign_class_roll_number",
 *   label = @Translation("Assign Class Roll Number"),
 *   type = ""
 * )
 */
class AssignRollNumber extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    // Do some processing..
    $year = date('y');
    $stream_code = current($entity->get('field_stream')->referencedEntities())->field_code->value;
    $year_sub = $year.$stream_code;
    $session_id = current($entity->get('field_session')->referencedEntities())->ID();
    $database = \Drupal::database();
    $query = $database->select('node__field_class_roll_no', 'fcr');
    $query->join('node_field_data', 'nd', 'fcr.entity_id = nd.nid');
    $query->join('node__field_session', 'fs', 'fs.entity_id = nd.nid');
    $query->condition('nd.type', 'admission_part_1');
    $query->condition('fs.field_session_target_id', $session_id);
    $query->condition('fcr.field_class_roll_no_value', $year_sub. "%", 'LIKE');
    $query->addExpression('MAX(fcr.field_class_roll_no_value)');
    $max_roll = $query->execute()->fetchField();

    if($max_roll){
      $class_roll = $max_roll += 1;
    }else{
      $roll = '001';
      $class_roll = $year.$stream_code.$roll;
    }
    $entity->set('field_class_roll_no', $class_roll);
    $entity->setNewRevision(FALSE);
    $entity->save();
    // Don't return anything for a default completion message, otherwise return translatable markup.
    return $this->t('Roll numbers has been assigned to selected items');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE);
    return $return_as_object ? $result : $result->isAllowed();
  }

}