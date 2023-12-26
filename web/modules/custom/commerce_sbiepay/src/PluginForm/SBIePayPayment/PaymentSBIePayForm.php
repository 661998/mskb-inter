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
    $success_url = Url::FromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
    $cancel_url = Url::FromRoute('commerce_payment.checkout.cancel', [
      'commerce_order' => $order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();

    //requestparameter 
    $orderid = $order_id;
    for ($i=0; $i<10; $i++)
    {
        $d=rand(1,30)%2;
        $d=$d ? chr(rand(65,90)) : chr(rand(48,57));
        $orderid=$orderid.$d;
    }
    $requestParameter = $merchant_id."|DOM|IN|".$cur."|".$amt."|Other|".$success_url."|".$cancel_url."|SBIEPAY|".$order_id."|2|NB|ONLINE|ONLINE";
    // $requestParameter = $merchant_id."|DOM|IN|".$cur."|".$amt."|Other|".$success_url."|".$cancel_url."|SBIEPAY|11YK9JRA6F0P|2|NB|ONLINE|ONLINE";

    $encrypt = new SBIePayEncryption();
    $parameters['EncryptTrans'] = $encrypt->encrypt($requestParameter, $merchant_key);
    $parameters['merchIdVal'] = $merchant_id;
    $parameters['submit'] = 'Submit';

    if ($mode == 'test') {
      $redirect_url = self::CCAVENUE_TEST_URL;
    } else {
      $redirect_url = self::CCAVENUE_LIVE_URL;
    }

    return $this->buildRedirectForm($form, $form_state, $redirect_url, $parameters, $redirect_method);
  }

}
