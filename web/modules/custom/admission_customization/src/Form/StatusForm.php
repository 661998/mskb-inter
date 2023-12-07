<?php

namespace Drupal\admission_customization\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\taxonomy\Entity\Term;


/**
 * Class StatusForm.
 */
class StatusForm extends FormBase {

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'status_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $session_list = $this->getTidListsId('session');
    $ugsettingManager = \Drupal::config('ug_settings.settings');
    $aCServices = \Drupal::service('admission_customization.services');
    $session = ($form_state->getValue('session')) ? $form_state->getValue('session') : $ugsettingManager->get('session_list');
    
    $data = [];
    $data['admission'] = $aCServices->admissionStatus($session);
    $data['registration'] = $aCServices->registrationStatus($session);
    
    $form['#prefix'] = '<div id="exposed_form"';
    $form['#suffix'] = '</div>';
    
    $form['session'] = array(
      '#title' => t('Session'),
      '#type' => 'select',
      '#options' => $session_list,
      '#default_value' => ($session != 0 ) ? $session : $ugsettingManager->get('session_list'),
      '#ajax' => [
        'wrapper' => 'exposed_form',
        'callback' => '::statusFormCallback',
        'event' => 'change',
      ],
    );

    $form['data'] = array(
      '#type' => 'value',
      '#value' => $data,
    );

    $form['#cache']['max-age'] = 0;
    $form['#attached']['library'][] = 'admission_customization/admission_customization';
    $form['#theme'] = 'status_form';

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
  }

  /**
   * Ajax callback.
   */
  public function statusFormCallback(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();
    $form_state->setValue('session', $values['session']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getTidListsId($vid) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vid);
    $query->sort('weight');
    $query->accessCheck(FALSE);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);
    foreach ($terms as $term) {
      $tid_list[$term->id()] = $term->name->value;
    }

    return $tid_list;
  }

}
