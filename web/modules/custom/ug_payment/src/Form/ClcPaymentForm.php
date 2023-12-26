<?php

namespace Drupal\ug_payment\Form;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Render\Markup;
use Drupal\commerce_order\Entity\Order;
use Drupal\ug_payment\UcPaymentContent;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ClcPaymentForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  
    public function getFormId() {
        return 'ug_payment_clc_block_form';
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node instanceof \Drupal\node\NodeInterface) {
          $nid = $node->id();
        }

        if($node->bundle() == 'clc'){
          $pay_type = 1;
        }else {
          $pay_type = 2;
        }
        // $pay_type = 1;

        $editurl = Url::fromRoute('entity.node.edit_form', ['node' => $nid]);
        $form['node_edit'] = [
          '#prefix' => '<div class="text-center mx-3">',
          '#suffix' => '</div>',
          '#type' => 'link',
          '#title' => $this->t('EDIT'),
          '#url' => $editurl,
          '#attributes' => ['class'=> 'button button--primary js-form-submit form-submit'],
        ];
        $ucpaymentManager = \Drupal::service('ug_payment.settings');
        $pid = $ucpaymentManager->getPidbyNidperclc();

        if (empty($pid)) {
          \Drupal::messenger()->addError('Fee amount not set. Please contact with College.');
        }else{

          $variation_details = $ucpaymentManager->getAmountbyPidperclc($pid, $pay_type);
          if (empty($variation_details['variation_id'])) {
            \Drupal::messenger()->addError('Fee amount not set. Please contact with College.');
          }else{
            $amount = $variation_details['amount'];
            $variation_id = $variation_details['variation_id'];
              
            $form['ug_pid'] = array(
              '#type' => 'hidden',
              '#value' => $pid,
            );
  
            $form['ug_variation_id'] = array(
              '#type' => 'hidden',
              '#value' => $variation_id,
            );
      
            $form['ug_nid'] = array(
              '#type' => 'hidden',
              '#value' => $nid,
            );
  
            $form['show_name'] = array(
              '#prefix' => '<div class="pay_instrucation">',
              '#markup' => 'Dear '.$node->getTitle(),
            );
      
            $form['actions']['submit'] = [
              '#type' => 'submit',
              '#value' => $this->t('Click here to Online Payment'),
              '#button_type' => 'primary',
            ];
  
            $form['show_details'] = array(
              '#markup' => 'of Rs. '.$amount.'/-',
              '#suffix' => '</div>',
            );
          }
        }
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $uid = \Drupal::currentUser()->id();
        $store_id = 1;
        $store = \Drupal::entityTypeManager()->getStorage('commerce_store')->load($store_id);
        $variation_id = $form_state->getValue('ug_variation_id');
        $nid = $form_state->getValue('ug_nid');
        $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

        $variationobj = \Drupal::entityTypeManager()
          ->getStorage('commerce_product_variation')
          ->load($variation_id);

        $cart_manager = \Drupal::service('commerce_cart.cart_manager');
        $cart_provider = \Drupal::service('commerce_cart.cart_provider');
        $cart = $cart_provider->getCart('default', $store);
        if (!$cart) {
          $cart = $cart_provider->createCart('default', $store);
        }

        if (!empty($cart)) {
            $cart_manager->emptyCart($cart);
        }

        $line_item = $cart_manager->addEntity($cart, $variationobj);

        // Get student profile
        $result = \Drupal::entityTypeManager()->getStorage('profile')
          ->loadByProperties([
            'uid' => $uid,
            'type' => 'customer',
          ]);
        $profile = current($result);
        if (!$profile) {
          $profileStorage = \Drupal::entityTypeManager()->getStorage('profile');
          $profile_data = [
            'type' => 'customer',
            'uid' => $uid,
            'status'=> 1,
            'field_mobile'=> $node_obj->field_mobile->value,
            'field_stream'=> $node_obj->field_stream->target_id,
            'address' => [
              "country_code" => "IN",
              "administrative_area" => $node_obj->get('field_permanent_address')->getValue()[0]['administrative_area'],
              "locality" => $node_obj->get('field_permanent_address')->getValue()[0]['locality'],
              "postal_code" => $node_obj->get('field_permanent_address')->getValue()[0]['postal_code'],
              "address_line1" => $node_obj->get('field_permanent_address')->getValue()[0]['address_line1'],
              "given_name" => $node_obj->getTitle(),
              "family_name" => $node_obj->getTitle(),
            ],
          ];
  
          $new_profile = $profileStorage->create($profile_data);
          $new_profile->save();
        }

        $database = \Drupal::database();
        $order_id = $database->query('SELECT order_id FROM {commerce_order} WHERE uid = :uid ORDER BY order_id DESC', array(':uid' => $uid))->fetchField();
        $order = \Drupal\commerce_order\Entity\Order::load($order_id);
        $order->set('field_order_type_nid', $nid);
        $order->save();

        $responce =  new RedirectResponse('/checkout');
        $responce->send();
    }

}