<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Provides an event registration form with AJAX.
 */
class EventRegistrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // 1. Add a Wrapper for styling
    $form['#prefix'] = '<div class="registration-page-wrapper"><div class="registration-card">';
    $form['#suffix'] = '</div></div>';

    // 2. Standard Fields
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
      '#attributes' => ['placeholder' => 'e.g. Rahul Sharma'],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
      '#attributes' => ['placeholder' => 'name@college.edu'],
    ];

    $form['college_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
      '#attributes' => ['placeholder' => 'e.g. IIT Bombay'],
    ];

    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
      '#attributes' => ['placeholder' => 'e.g. Computer Science'],
    ];

    // --- AJAX LOGIC ---
    $database = Database::getConnection();
    // Fetch unique categories
    $categories = $database->query("SELECT DISTINCT event_category, event_category FROM {event_config}")->fetchAllKeyed();
    $selected_category = $form_state->getValue('event_category');

    $form['event_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category of the Event'),
      '#options' => $categories,
      '#empty_option' => $this->t('- Select Category -'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateDateDropdown',
        'wrapper' => 'date-wrapper',
        'event' => 'change',
      ],
    ];

    $form['date_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'date-wrapper'],
    ];

    $date_options = [];
    if ($selected_category) {
      $query = $database->select('event_config', 'e');
      $query->fields('e', ['event_date', 'event_date']);
      $query->condition('event_category', $selected_category);
      $query->distinct();
      $date_options = $query->execute()->fetchAllKeyed();
    }

    $form['date_wrapper']['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $date_options,
      '#empty_option' => $this->t('- Select Date -'),
      '#validated' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventNameDropdown',
        'wrapper' => 'event-name-wrapper',
        'event' => 'change',
      ],
    ];

    $selected_date = $form_state->getValue('event_date');

    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $event_options = [];
    if ($selected_category && $selected_date) {
      $query = $database->select('event_config', 'e');
      $query->fields('e', ['id', 'event_name']);
      $query->condition('event_category', $selected_category);
      $query->condition('event_date', $selected_date);
      $event_options = $query->execute()->fetchAllKeyed();
    }

    $form['event_name_wrapper']['event_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $event_options,
      '#empty_option' => $this->t('- Select Event -'),
      '#validated' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register Now'),
      '#attributes' => ['class' => ['register-btn']],
    ];

    // Attach the CSS library
    $form['#attached']['library'][] = 'event_registration/registration_style';

    return $form;
  }

  /**
   * AJAX Callback for Date Dropdown.
   */
  public function updateDateDropdown(array &$form, FormStateInterface $form_state) {
    return $form['date_wrapper'];
  }

  /**
   * AJAX Callback for Event Name Dropdown.
   */
  public function updateEventNameDropdown(array &$form, FormStateInterface $form_state) {
    return $form['event_name_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // 1. Validate special characters in names
    $name = $form_state->getValue('full_name');
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
      $form_state->setErrorByName('full_name', $this->t('Full Name can only contain letters and spaces.'));
    }

    // 2. Check for duplicate registration
    $email = $form_state->getValue('email');
    $date = $form_state->getValue('event_date');
    
    $database = Database::getConnection();
    $query = $database->select('event_registration', 'er');
    $query->fields('er', ['id']);
    $query->condition('email', $email);
    $query->condition('event_date', $date);
    $result = $query->execute()->fetchField();

    if ($result) {
      $form_state->setErrorByName('email', $this->t('This email address is already registered for an event on this date.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $event_id = $form_state->getValue('event_id');
    $database = Database::getConnection();
    $event_name = $database->query("SELECT event_name FROM {event_config} WHERE id = :id", [':id' => $event_id])->fetchField();

    $database->insert('event_registration')
      ->fields([
        'full_name' => $form_state->getValue('full_name'),
        'email' => $form_state->getValue('email'),
        'college_name' => $form_state->getValue('college_name'),
        'department' => $form_state->getValue('department'),
        'event_category' => $form_state->getValue('event_category'),
        'event_date' => $form_state->getValue('event_date'),
        'event_name' => $event_name,
        'event_id' => $event_id,
        'created' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();

    // Send Email
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'event_registration';
    $key = 'registration_confirmation';
    $to = $form_state->getValue('email');
    $params['name'] = $form_state->getValue('full_name');
    $params['event_name'] = $event_name;
    $params['category'] = $form_state->getValue('event_category');
    $params['date'] = $form_state->getValue('event_date');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    if ($result['result'] !== TRUE) {
      $this->messenger()->addError($this->t('There was a problem sending your confirmation email.'));
    }
    else {
      $this->messenger()->addStatus($this->t('Registration successful! Confirmation sent.'));
    }
  }

}