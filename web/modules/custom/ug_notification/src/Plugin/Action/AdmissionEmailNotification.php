<?php

namespace Drupal\ug_notification\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Action description.
 *
 * @Action(
 *   id = "admission_email_notification",
 *   label = @Translation("Admission Email Notification"),
 *   type = ""
 * )
 */
class AdmissionEmailNotification extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    // Do some processing..
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    $ugsettingManager = \Drupal::config('ug_settings.settings');
    $subject = $ugsettingManager->get('ad_email_subject');
    $body = $ugsettingManager->get('ad_email_body');
    $count = $unsuccessful = 0 ;

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ug_notification';
    $key = 'add_notification';
    $params['subject'] = $subject;
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    foreach ($entities as $delta => $entity) {
      $roll = $entity->get('field_class_roll_no')->value;
      $to = $entity->get('field_email_id')->value;
      $name = $entity->getTitle();
      $params['message'] = t($body, array('@name' => $name,'@roll' => $roll));
      
      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] != true) {
        $unsuccessful++;
        $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
        \Drupal::logger('ug_notification')->error($message);
      }else{
        $now = new DrupalDateTime('now');
        $entity->set('field_ad_email_notification', '1');
        $entity->set('field_ad_email_notification_date', $now->format('Y-m-d\TH:i:s'));
        $entity->setNewRevision(FALSE);
        $entity->save();
        $count ++;
      }
      
    }

    if($unsuccessful != 0){
      \Drupal::messenger()->addError(t('@var Email notification not sent.', ['@var' => $unsuccessful]));
    }

    \Drupal::messenger()->addMessage(t('@count Email notification sent.', ['@count' => $count])); 
    
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE);
    return $return_as_object ? $result : $result->isAllowed();
  }

}