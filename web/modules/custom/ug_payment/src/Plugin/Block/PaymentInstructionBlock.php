<?php

namespace Drupal\ug_payment\Plugin\Block;

use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "payment_instruction_block",
 *   admin_label = @Translation("Payment Instruction"),
 *   category = @Translation("Payment Instruction"),
 * )
 */
class PaymentInstructionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    return 0;
  }

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
      $ugsettingManager = \Drupal::config('ug_settings.settings');
      $session_id = $ugsettingManager->get('session_list');

      if(($node_obj->field_payment_received->value != 1) && ($uid == $node_uid)){
        // Disable Payment if Admission is closed
        if($node->bundle() == 'admission_part_1' && (
            empty($ugsettingManager->get('admission_part_1')) || 
            $node->get('field_session')->target_id != $session_id
          )
          ){
          return [
            '#markup' => $this->t('<b class="text-danger">Admission is now closed for this session/Stream.</b>')
          ];
        }

        // Disable Payment if Registration is closed
        if($node->bundle() == 'registration' && (
            empty($ugsettingManager->get('registration')) || 
            $node->get('field_session')->target_id != $ugsettingManager->get('registration_session')
          )
          ){
          return [
            '#markup' => $this->t('<b class="text-danger">Registration is now closed for this session.</b>')
          ];
        }

        // Call Payment form
        $form = \Drupal::formBuilder()->getForm('Drupal\ug_payment\Form\PaymentInstructionForm');
        return $form;
        
      } elseif((!$node_obj->get('field_document_verification')->isEmpty() && 
        ($node_obj->field_document_verification->value == 'Not Verified' || $node_obj->field_document_verification->value == 'Rejected')) && 
        $uid == $node_uid) {
          
        $editurl = Url::fromRoute('entity.node.edit_form', ['node' => $nid]);
        $form['node_edit'] = [
          '#prefix' => '<div class="text-center mx-3">',
          '#suffix' => '</div>',
          '#type' => 'link',
          '#title' => $this->t('EDIT'),
          '#url' => $editurl,
          '#attributes' => ['class'=> 'button button--primary js-form-submit form-submit'],
        ];
        return $form;

      } elseif(($node_obj->field_payment_received->value == 1) && (($uid == $node_uid) || !in_array('student',$roles))) {
        $node_type = $node->bundle();
        global $base_url;
        if($node_type == 'admission_part_1'){
          $pdfurl = Url::fromUri($base_url.'/admission-part-1-pdf/'.$nid);
          $instruct_text = 'नोट: नामांकन प्रपत्र भरकर सभी कागजातों सहित हार्ड कॉपी महाविद्यालय कार्यालय में जमा करें अन्यथा नामांकन मान्य नहीं समझा जायेगा.';
        }elseif($node_type == 'registration'){
          $pdfurl = Url::fromUri($base_url.'/registration-pdf/'.$nid);
          $instruct_text = 'नोट: पंजीकरण प्रपत्र भरकर सभी कागजातों सहित हार्ड कॉपी महाविद्यालय कार्यालय में जमा करें अन्यथा पंजीकरण मान्य नहीं समझा जायेगा.';
        }elseif($node_type == 'examination_part_1'){
          $pdfurl = Url::fromUri($base_url.'/examination-part-1-pdf/'.$nid);
          $instruct_text = 'नोट: परीक्षा प्रपत्र भरकर सभी कागजातों सहित हार्ड कॉपी महाविद्यालय कार्यालय में जमा करें अन्यथा परीक्षा प्रपत्र मान्य नहीं समझा जायेगा.';
        }else{
          $pdfurl = Url::fromUri($base_url.'/examination-part-2-pdf/'.$nid);
          $instruct_text = 'नोट: परीक्षा प्रपत्र भरकर सभी कागजातों सहित हार्ड कॉपी महाविद्यालय कार्यालय में जमा करें अन्यथा परीक्षा प्रपत्र मान्य नहीं समझा जायेगा.';
        }
        $form['print_pdf'] = [
          '#prefix' => '<div class="text-center mx-3">',
          '#suffix' => '</div>',
          '#type' => 'link',
          '#title' => $this->t('<i class="fa fa-print" aria-hidden="true"></i> Print'),
          '#url' => $pdfurl,
          '#attributes' => ['class'=> 'button button--primary js-form-submit form-submit'],
        ];
        $form['instruction_text'] = [
          '#type' => 'item',
          '#markup' => $instruct_text
        ];

        return $form;
      }
    }
  }
}
