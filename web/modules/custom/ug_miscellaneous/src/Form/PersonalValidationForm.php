<?php

namespace Drupal\ug_miscellaneous\Form;

use Drupal;
use Drupal\user\Entity\User;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PersonalValidationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'personal_validation_block_form';
  }
    
  /**
   * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['application_no'] = array(
      '#type' => 'textfield',
      '#title' => 'OFSS Reference No',
      '#size' => 60, 
      '#maxlength' => 15, 
      '#required' => TRUE,
    );

    $form['mobile_number'] = array(
      '#type' => 'textfield',
      '#title' => 'Mobile Number',
      '#size' => 60, 
      '#maxlength' => 10, 
      '#required' => TRUE,
    );

    $stream_list = $this->get_tid_lists_id('stream');
    $form['stream'] = array(
      '#type' => 'select',
      '#title' => t('Stream'),
      '#options' => $stream_list,
      '#required' => TRUE,
    );
     
    $form['category'] = array(
      '#type' => 'select',
      '#title' => t('Category'),
      '#required' => TRUE,
      '#options' => array(
        'GEN' => t('GEN'),
        'SC' => t('SC'),
        'ST' => t('ST'),
        'BC' => t('BC'),
        'EBC' => t('EBC'),
      ),
    );
  
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function get_tid_lists_id($vid) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vid);
    $query->sort('weight');
    $query->accessCheck(FALSE);
    $tids = $query->execute();
    $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
    foreach ($terms as $term) {
      $tid_list[$term->id()] = $term->name->value;
    }

    return $tid_list;
  }

  public function check_personal_data_exists($application_no, $mobile_number, $stream, $category) {
    $results = \Drupal::entityQuery('node')
      ->condition('type', 'admission_part_1')
      ->condition('field_application_no', $application_no)
      ->condition('field_mobile', $mobile_number)
      ->condition('field_stream', $stream)
      ->condition('field_caste_category', $category)
      ->accessCheck(TRUE)
      ->execute();

    return $results;
  }

  /**
   * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $application_no = $form_state->getValue('application_no');
    $mobile_number = $form_state->getValue('mobile_number');
    $stream = $form_state->getValue('stream');
    $category = $form_state->getValue('category');

    if(!empty($mobile_number)){
      $total_length = strlen($mobile_number);
      if($total_length != 10){
        $form_state->setErrorByName('mobile_number', t('Mobile Number should be of 10 digit.'));
      }
    }

    $results = $this->check_personal_data_exists($application_no, $mobile_number, $stream, $category);
    if(empty($results)){
      $form_state->setErrorByName('application_no', t('Your Details not found.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $application_no = $form_state->getValue('application_no');
    $mobile_number = $form_state->getValue('mobile_number');
    $stream = $form_state->getValue('stream');
    $category = $form_state->getValue('category');
    $results = $this->check_personal_data_exists($application_no, $mobile_number, $stream, $category);
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->load(current($results));
    $uid = $nodes->getOwnerId();
    $user = User::load($uid);
    $user_roles = $user->getRoles();
    if (!in_array('administrator', $user_roles) 
      && !in_array('college_admin', $user_roles)
      && in_array('student', $user_roles)) {
      user_login_finalize($user);
    }
    $response =  new RedirectResponse('/');
    $request  = \Drupal::request();
    $request->getSession()->save();
    $response->prepare($request);
    \Drupal::service('kernel')->terminate($request, $response);
    $response->send();
  }
}
