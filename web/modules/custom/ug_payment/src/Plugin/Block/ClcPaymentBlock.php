<?php

namespace Drupal\ug_payment\Plugin\Block;

use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CLC' Block.
 *
 * @Block(
 *   id = "clc_payment_instruction_block",
 *   admin_label = @Translation("CLC Payment Instruction"),
 *   category = @Translation("CLC Payment Instruction"),
 * )
 */
class ClcPaymentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
      $node_uid = $node->getOwnerId();
      $uid = \Drupal::currentUser()->id();
      $user = User::load($uid);
      $roles = $user->getRoles();

      $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      if(($node_obj->field_payment_received->value != 1) && ($uid == $node_uid)){
        $form = \Drupal::formBuilder()->getForm('Drupal\ug_payment\Form\ClcPaymentForm');
        $form['#cache']['max-age'] = 0;
        return $form;
      }elseif (($node_obj->field_payment_received->value == 1) && (($uid == $node_uid) || !in_array('student',$roles))) {
        $node_type = $node->bundle();
        global $base_url;
        $pdfurl = Url::fromUri($base_url.'/clcreceipt-pdf/'.$nid);
        $form['print_pdf'] = [
          '#prefix' => '<div class="text-center mx-3">',
          '#suffix' => '</div>',
          '#type' => 'link',
          '#title' => $this->t('<i class="fa fa-print" aria-hidden="true"></i> Print'),
          '#url' => $pdfurl,
          '#attributes' => ['class'=> 'button button--primary js-form-submit form-submit'],
        ];
        $form['#cache']['max-age'] = 0;

        return $form;
      }
    }
  }
}
