<?php

namespace Drupal\commerce_sbiepay\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_sbiepay\SBIePayEncryption;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Messenger\MessengerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides the Off-site Redirect payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "sbiepay_payment",
 *   label = "SBIePay",
 *   display_label = "SBIePay",
 *    forms = {
 *     "offsite-payment" =
 *   "Drupal\commerce_sbiepay\PluginForm\SBIePayPayment\PaymentSBIePayForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa",
 *   },
 *   requires_billing_information = FALSE,
 * )
 */
class SBIePayPayment extends OffsitePaymentGatewayBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $supp_currency = [
      'INR' => 'Indian Rupee',
      'USD' => 'United States Dollar',
      'SGD' => 'Singapore Dollar',
      'GBP' => 'Pound Sterling',
      'EUR' => 'Euro',
    ];
    $form['merchant_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configuration['merchant_id'],
      '#required' => TRUE,
    ];
    $form['merchant_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant Key'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $this->configuration['merchant_key'],
      '#required' => TRUE,
    ];

    $form['currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Default Currency'),
      '#options' => $supp_currency,
      '#default_value' => (isset($this->configuration['currency'])) ? $this->configuration['currency'] : 'INR',
    ];
    $form['language'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Language'),
      '#default_value' => 'EN',
    ];
    $form['pmode'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Mode'),
      '#default_value' => $this->getMode(),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['merchant_id'] = $values['merchant_id'];
      $this->configuration['merchant_key'] = $values['merchant_key'];
      $this->configuration['currency'] = $values['currency'];
      $this->configuration['language'] = $values['language'];
      $this->configuration['pmode'] = $values['pmode'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
    $response = array();
    $encResponse = $request->get('encData');
    $decrypt = new SBIePayEncryption();
    $rcvdString = $decrypt->decrypt($encResponse, $this->configuration['merchant_key']);
    $response = explode('|', $rcvdString);
    // dd($decryptValues);
    // for ($i = 0; $i < count($decryptValues); $i++) {
    //   $information = explode('=', $decryptValues[$i]);
    //   if (count($information) == 2) {
    //     $response[$information[0]] = $information[1];
    //   }
    // }
    // dd($response);

    switch ($response[2]) {

      case 'SUCCESS':
      // if($order->id() == $response['order_id'] && round($order->getTotalPrice()->getNumber(), 2) == round($response['mer_amount'], 2)){
        if(round($order->getTotalPrice()->getNumber(), 2) == round($response[3], 2)){
        $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
        $payment = $payment_storage->create([
          'state' => 'authorization',
          'amount' => $order->getTotalPrice(),
          'payment_gateway' => $this->parentEntity->id(),
          'order_id' => $order->id(),
          'test' => $this->getMode() == 'test',
          'remote_id' => $response[1],
          'remote_state' => $response[2],
          'authorized' => $this->time->getRequestTime(),
        ]);
        $payment->save();
        $this->messenger()->addMessage(t('Your payment was successful with Order Id : @orderid and Transaction Id : @transaction_id', [
          '@orderid' => $order->id(),
          '@transaction_id' => $response[1],
        ]));
        
        $total_amount = intval($order->total_price->number);
        $current_date = date('Y-m-d', time());
        $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load($order->field_order_type_nid->value);
        $variation_id = '';
        foreach ($order->getItems() as $order_item) {
          $product_variation = $order_item->getPurchasedEntity();
          $variation_id = $product_variation->get('variation_id')->value;
        }

        if($node_obj->field_payment_received->value == 3){
   	      $node_obj->field_utr_ref_number_2 = $response[1];
          $node_obj->field_less_payment_amount = $total_amount;
          $node_obj->field_payment_received = 1;
          $node_obj->field_payment_date2 = $current_date;
          $node_obj->field_related_fee = $variation_id;
     	}else{
          $node_obj->field_utr_ref_number = $response[1];
          $node_obj->field_payment_amount = $total_amount;
          $node_obj->field_payment_received = 1;
          $node_obj->field_payment_date = $current_date;
          $node_obj->field_related_fee = $variation_id;
   	    }
   	    $node_obj->save();
      }else{
        $this->messenger()->addError(t('Security error : Illegal access detected. Please contact administrator.'));
      }
      break;

      case 'Aborted':
      $this->messenger()->addError(t('The transaction has been aborted.'));
      break;

      case 'Failure':
      $this->messenger()->addError(t('The transaction has been declined.'));
      break;

      default:
      $this->messenger()->addError(t('Payment has been failed. Please try again or choose a different payment option.'));
      break;
    }
    
     $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$order->field_order_type_nid->value);

   	 $response = new RedirectResponse($path_alias);
     $response->send();
     return;
  }

  /**
   * {@inheritdoc}
   */
  public function onCancel(OrderInterface $order, Request $request) {
    $status = $request->get('status');
    $this->messenger()->addError(t('Payment @status on @gateway but may resume the checkout process here when you are ready.', [
      '@status' => $status,
      '@gateway' => $this->getDisplayLabel(),
    ]));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'merchant_id' => '',
      'merchant_key' => '',
    ];
  }

}
