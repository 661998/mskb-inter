<?php

namespace Drupal\ug_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\Role;
use Drupal\taxonomy\Entity\Term;

/**
 * Class SettingsForm.
 */
class UgSettingsForm extends ConfigFormBase {
/** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'ug_settings.settings';

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ug_settings_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	  $config = $this->config(static::SETTINGS);
    $merit_list = $this->get_tid_lists_id('merit_list');
    $session_list = $this->get_tid_lists_id('session');

    $form['part1_add'] = array(
      '#type' => 'details',
      '#title' => t('Admission'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['part1_add']['admission_part_1'] = array(
      '#type' => 'checkbox',
      '#title' => t('Admission'),
      '#default_value' => $config->get('admission_part_1'),
      '#attributes' => [
        'name' => 'field_admission_part1',
      ],
    );

    $form['part1_add']['session_list'] = array(
      '#title' => t('Session'),
      '#type' => 'select',
      '#description' => 'Select Session',
      '#options' => $session_list,
      '#default_value' => $config->get('session_list'),
      '#attributes' => [
        'id' => 'session-list',
      ],
      '#states' => [
        'visible' => [
          ':input[name="field_admission_part1"]' => ['checked' => TRUE],
        ],
      ],
    );

    $form['part1_add']['merit_list'] = array(
      '#title' => t('Merit List'),
      '#type' => 'select',
      '#description' => 'Select merit list',
      '#options' => $merit_list,
      '#default_value' => $config->get('merit_list'),
      '#attributes' => [
        'id' => 'merit-list',
      ],
      '#states' => [
        'visible' => [
          ':input[name="field_admission_part1"]' => ['checked' => TRUE],
        ],
      ],
    );

    $form['part1_add']['admission_part_1_latefine'] = array(
      '#type' => 'checkbox',
      '#title' => t('Late Fine'),
      '#default_value' => $config->get('admission_part_1_latefine'),
      '#states' => [
        'visible' => [
          ':input[name="field_admission_part1"]' => ['checked' => TRUE],
        ],
      ],
    );

    // Registration
    $form['reg'] = array(
      '#type' => 'details',
      '#title' => t('Registration'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['reg']['registration'] = array(
      '#type' => 'checkbox',
      '#title' => t('Registration'),
      '#default_value' => $config->get('registration'),
      '#attributes' => [
        'name' => 'field_registration',
      ],
    );

    $form['reg']['registration_session'] = array(
      '#title' => t('Session'),
      '#type' => 'select',
      '#description' => 'Select Session',
      '#options' => $session_list,
      '#default_value' => $config->get('registration_session'),
      '#attributes' => [
        'id' => 'session-list',
      ],
      '#states' => [
        'visible' => [
          ':input[name="field_registration"]' => ['checked' => TRUE],
        ],
      ],
    );

    $form['reg']['registration_latefine'] = array(
      '#type' => 'checkbox',
      '#title' => t('Late Fine'),
      '#default_value' => $config->get('registration_latefine'),
      '#states' => [
        'visible' => [
          ':input[name="field_registration"]' => ['checked' => TRUE],
        ],
      ],
    );

    // Part 1 Examination
    $form['part1_exam'] = array(
      '#type' => 'details',
      '#title' => t('Part1 Examination'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['part1_exam']['examination_part_1'] = array(
      '#type' => 'checkbox',
      '#title' => t('Examination Part 1'),
      '#default_value' => $config->get('examination_part_1'),
      '#attributes' => [
        'name' => 'field_examination_part1',
      ],
    );

    $form['part1_exam']['p1exam_session_list'] = array(
      '#title' => t('Session'),
      '#type' => 'select',
      '#description' => 'Select Session',
      '#options' => $session_list,
      '#default_value' => $config->get('p1exam_session_list'),
      '#attributes' => [
        'id' => 'p1exam-session-list',
      ],
      '#states' => [
        'visible' => [
          ':input[name="field_examination_part1"]' => ['checked' => TRUE],
        ],
      ],
    );

    $form['part1_exam']['examination_part_1_latefine'] = array(
      '#type' => 'checkbox',
      '#title' => t('Late Fine'),
      '#default_value' => $config->get('examination_part_1_latefine'),
      '#states' => [
        'visible' => [
          ':input[name="field_examination_part1"]' => ['checked' => TRUE],
        ],
      ],
    );

    // Part 2 Examination
    $form['part2_exam'] = array(
      '#type' => 'details',
      '#title' => t('Part2 Examination'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['part2_exam']['examination_part_2'] = array(
      '#type' => 'checkbox',
      '#title' => t('Examination Part 2'),
      '#default_value' => $config->get('examination_part_2'),
      '#attributes' => [
        'name' => 'field_examination_part2',
      ],
    );

    $form['part2_exam']['p2exam_session_list'] = array(
      '#title' => t('Session'),
      '#type' => 'select',
      '#description' => 'Select Session',
      '#options' => $session_list,
      '#default_value' => $config->get('p2exam_session_list'),
      '#attributes' => [
        'id' => 'p2exam-session-list',
      ],
      '#states' => [
        'visible' => [
          ':input[name="field_examination_part2"]' => ['checked' => TRUE],
        ],
      ],
    );

    $form['part2_exam']['examination_part_2_latefine'] = array(
      '#type' => 'checkbox',
      '#title' => t('Late Fine'),
      '#default_value' => $config->get('examination_part_2_latefine'),
      '#states' => [
        'visible' => [
          ':input[name="field_examination_part2"]' => ['checked' => TRUE],
        ],
      ],
    );

    // Certificate
    $form['certificate'] = array(
      '#type' => 'details',
      '#title' => t('Certificate'),
    );

    $form['certificate']['clc'] = array(
      '#type' => 'checkbox',
      '#title' => t('CLC & Character Certificate'),
      '#default_value' => $config->get('clc'),
    );

    $form['certificate']['transfer_certificate'] = array(
      '#type' => 'checkbox',
      '#title' => t('Transfer Certificate'),
      '#default_value' => $config->get('transfer_certificate'),
    );

    $form['notification'] = array(
      '#type' => 'details',
      '#title' => t('Notification'),
    );

    $form['notification']['sem_1'] = array(
      '#type' => 'details',
      '#title' => t('Semester 1 Admission'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['notification']['sem_1']['sms_api_key'] = array(
      '#type' => 'textfield', 
      '#title' => t('SMS API Key'), 
      '#default_value' => $config->get('sms_api_key'), 
      '#size' => 60, 
    );

    $form['notification']['sem_1']['sms_sender'] = array(
      '#type' => 'textfield', 
      '#title' => t('SMS Sender'), 
      '#default_value' => $config->get('sms_sender'), 
      '#size' => 60, 
    );

    $form['notification']['sem_1']['ad_sms_template'] = array(
      '#type' => 'textfield', 
      '#title' => t('SMS Template'), 
      '#default_value' => $config->get('ad_sms_template'), 
      '#size' => 60,
      '#description' => t('@name => Student name, @roll => Roll Number'),
    );

    $form['notification']['sem_1']['ad_email_notification']['ad_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Email Subject'),
      '#default_value' => $config->get('ad_email_subject'),
      '#required' => TRUE,
    );
    $form['notification']['sem_1']['ad_email_notification']['ad_email_body'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Email Body'),
      '#format' => 'basic_html',
      '#allowed_formats' => array('basic_html'),
      '#default_value' => $config->get('ad_email_body'),
      '#description' => t('Use token: @name for Name of user.'),
    );
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function get_tid_lists_name($vid) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vid);
    $query->sort('weight');
    $query->accessCheck(FALSE);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);
    foreach ($terms as $term) {
      $tid_list[$term->name->value] = $term->name->value;
    }

    return $tid_list;
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
    $terms = Term::loadMultiple($tids);
    foreach ($terms as $term) {
      $tid_list[$term->id()] = $term->name->value;
    }

    return $tid_list;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $list = [];
    $config = $this->configFactory->getEditable(static::SETTINGS);
    $input = $form_state->getUserInput();
    $admission_part1 = isset($input['field_admission_part1']) ? $input['field_admission_part1'] : '';
    $registration = isset($input['field_registration']) ? $input['field_registration'] : '';
    $examination_part1 = isset($input['field_examination_part1']) ? $input['field_examination_part1'] : '';
    $examination_part2 = isset($input['field_examination_part2']) ? $input['field_examination_part2'] : '';
    $examination_part3 = isset($input['field_examination_part3']) ? $input['field_examination_part3'] : '';

    $config->set('admission_part_1', $admission_part1);
    $config->set('merit_list', $form_state->getValue('merit_list'));
    $config->set('session_list', $form_state->getValue('session_list'));
    $config->set('admission_part_1_latefine', $form_state->getValue('admission_part_1_latefine'));

    $config->set('registration', $registration);
    $config->set('registration_session', $form_state->getValue('registration_session'));
    $config->set('registration_latefine', $form_state->getValue('registration_latefine'));
    
    $config->set('examination_part_1', $examination_part1);
    $config->set('p1exam_session_list', $form_state->getValue('p1exam_session_list'));
    $config->set('examination_part_1_latefine', $form_state->getValue('examination_part_1_latefine'));

    $config->set('examination_part_2', $examination_part2);
    $config->set('p2exam_session_list', $form_state->getValue('p2exam_session_list'));
    $config->set('examination_part_2_latefine', $form_state->getValue('examination_part_2_latefine'));

    $config->set('clc', $form_state->getValue('clc'));
    $config->set('transfer_certificate', $form_state->getValue('transfer_certificate'));

    $config->set('sms_api_key', $form_state->getValue('sms_api_key'));
    $config->set('sms_sender', $form_state->getValue('sms_sender'));
    $config->set('ad_sms_template', $form_state->getValue('ad_sms_template'));
    $config->set('ad_email_subject', $form_state->getValue('ad_email_subject'));
    $config->set('ad_email_body', $form_state->getValue('ad_email_body')['value']);
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
