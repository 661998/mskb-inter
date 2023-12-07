<?php

namespace Drupal\registration_customization\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Action description.
 *
 * @Action(
 *   id = "registration_send_to_board",
 *   label = @Translation("Send to Board"),
 *   type = ""
 * )
 */
class SendToBoard extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    // Do some processing..
    $entity->set('field_send_to_board', '1');
    $entity->save();
    // Don't return anything for a default completion message, otherwise return translatable markup.
    return $this->t('Sent to board list.');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE);
    return $return_as_object ? $result : $result->isAllowed();
  }

}