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
 *   id = "admission_sms_notification",
 *   label = @Translation("Admission SMS Notification"),
 *   type = ""
 * )
 */
class AdmissionSmsNotification extends ViewsBulkOperationsActionBase {

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
    $smsApiKey = urlencode($ugsettingManager->get('sms_api_key'));
    $smsSender = urlencode($ugsettingManager->get('sms_sender'));
    $template = $ugsettingManager->get('ad_sms_template');
    $count = $unsuccessful = 0 ;


    foreach ($entities as $delta => $entity) {
      $roll = $entity->get('field_class_roll_no')->value;
      $mobile = $entity->get('field_mobile')->value;
      $name = $entity->getTitle();
      
      $sms = t($template, array('@name' => substr($name,0,15),'@roll' => $roll));
      $numbers = array($mobile);
      $message = rawurlencode($sms);
      $numbers = implode(',', $numbers);
      // $data = array('apikey' => $smsApiKey, 'numbers' => $numbers, 'sender' => $smsSender, 'message' => $message);
      // $ch = curl_init('https://api.textlocal.in/send/');
      // curl_setopt($ch, CURLOPT_POST, true);
      // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // $response = curl_exec($ch);
      // $err = curl_error($ch);
      // curl_close($ch);
      // if($err){
      //   $unsuccessful++;
      // }else{
          // $now = new DrupalDateTime('now');
          // $entity->set('field_ad_sms_notification', '1');
          // $entity->set('field_ad_sms_notification_date', $now->format('Y-m-d\TH:i:s'));
          // $entity->setNewRevision(FALSE);
          // $entity->save();
          // $count ++;
      // }
      
    }
    
    if($unsuccessful != 0){
      \Drupal::messenger()->addError(t('@var SMS notification not sent.', ['@var' => $unsuccessful]));
    }

    \Drupal::messenger()->addMessage(t('@count SMS notification sent.', ['@count' => $count]));
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE);
    return $return_as_object ? $result : $result->isAllowed();
  }

}