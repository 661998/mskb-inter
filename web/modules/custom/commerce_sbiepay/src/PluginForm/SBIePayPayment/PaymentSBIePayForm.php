<?php

namespace Drupal\commerce_sbiepay\PluginForm\SBIePayPayment;

use Drupal\commerce_sbiepay\SBIePayEncryption;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\commerce_order\Entity\Order;

class PaymentSBIePayForm extends BasePaymentOffsiteForm {

  const CCAVENUE_LIVE_URL = 'https://sbiepay.sbi/secure/AggregatorHostedListener';

  const CCAVENUE_TEST_URL = 'https://test.sbiepay.sbi/secure/AggregatorHostedListener';

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    $redirect_method = 'post';
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

    $order_id = \Drupal::routeMatch()->getParameter('commerce_order')->id();
    $order = Order::load($order_id);
    $billing_profile = $order->getBillingProfile();
    if ($billing_profile) {
      $address = $billing_profile->address->first();
    }

    $stream_id = $billing_profile->field_stream->target_id;
    $stream_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($stream_id);
    $stream_name = $stream_obj->name->value;
    $notes = 'Stream-'.$stream_name;

    $mode = $payment_gateway_plugin->getConfiguration()['pmode'];
    $merchant_id = $payment_gateway_plugin->getConfiguration()['merchant_id'];
    $merchant_key = $payment_gateway_plugin->getConfiguration()['merchant_key'];
    $cur = $payment_gateway_plugin->getConfiguration()['currency'];
    $lng = $payment_gateway_plugin->getConfiguration()['language'];
    $amt = round($payment->getAmount()->getNumber(), 2);
    $redirect_url = Url::FromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
    $cancel_url = Url::FromRoute('commerce_payment.checkout.cancel', [
      'commerce_order' => $order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();

    //requestparameter 
    $requestParameter = $merchant_id."|DOM|IN|".$cur."|".$amt."|Other|".$redirect_url."|".$cancel_url."|SBIEPAY|".$order_id."|2|NB|ONLINE|ONLINE";

    // $parameters = [
    //   'merchant_id' => $merchant_id,
    //   'order_id' => $order_id,
    //   'tid' => time(),
    //   'amount' => round($payment->getAmount()->getNumber(), 2),
    //   'currency' => $payment->getAmount()->getCurrencyCode(),
    //   'language' => $lng,
    //   'redirect_url' => Url::FromRoute('commerce_payment.checkout.return', [
    //     'commerce_order' => $order_id,
    //     'step' => 'payment',
    //   ], ['absolute' => TRUE])->toString(),
    //   'cancel_url' => Url::FromRoute('commerce_payment.checkout.cancel', [
    //     'commerce_order' => $order_id,
    //     'step' => 'payment',
    //   ], ['absolute' => TRUE])->toString(),
    //   'billing_name' => isset($address) ? $address->getGivenName() : "",
    //   'billing_address' => isset($address) ? $address->getAddressLine1() : "",
    //   'billing_city' => isset($address) ? $address->getLocality() : "",
    //   'billing_state' => isset($address) ? $address->getAdministrativeArea() : "",
    //   'billing_country' => isset($address) ? \Drupal::service('country_manager')->getList()[$address->getCountryCode()]->__toString() : "",
    //   'billing_zip' => isset($address) ? $address->getPostalCode() : "",
    //   'billing_email' => isset($address) ? $order->getEmail() : "",
    //   'billing_tel' => $billing_profile->field_mobile->value,
    //   'billing_notes' => $notes
    // ];

    // $merchantData = '';
    // foreach ($parameters as $key => $value) {
    //   $merchantData .= $key . '=' . $value . '&';
    // }

    $encrypt = new SBIePayEncryption();
    $parameters['EncryptTrans'] = $encrypt->encrypt($requestParameter, $working_key);
    $parameters['merchIdVal'] = $merchant_id;

    if ($mode == 'test') {
      $redirect_url = self::CCAVENUE_TEST_URL;
    } else {
      $redirect_url = self::CCAVENUE_LIVE_URL;
    }

    return $this->buildRedirectForm($form, $form_state, $redirect_url, $parameters, $redirect_method);
  }

}
