<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Response;

class AdminListingForm extends FormBase {

  public function getFormId() {
    return 'event_registration_admin_listing';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $database = Database::getConnection();

    // 1. Event Date Dropdown
    $dates = $database->query("SELECT DISTINCT event_date, event_date FROM {event_config}")->fetchAllKeyed();
    
    $selected_date = $form_state->getValue('filter_date');

    $form['filter_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Event Date'),
      '#options' => $dates,
      '#empty_option' => $this->t('- Select Date -'),
      '#ajax' => [
        'callback' => '::updateEventDropdown',
        'wrapper' => 'event-wrapper',
        'event' => 'change',
      ],
    ];

    // 2. Event Name Dropdown (Updates via AJAX)
    $form['event_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-wrapper'],
    ];

    $events = [];
    if ($selected_date) {
      $events = $database->query("SELECT id, event_name FROM {event_config} WHERE event_date = :date", [':date' => $selected_date])->fetchAllKeyed();
    }

    $form['event_wrapper']['filter_event'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Event Name'),
      '#options' => $events,
      '#empty_option' => $this->t('- Select Event -'),
      '#validated' => TRUE,
      '#ajax' => [
        'callback' => '::updateTable',
        'wrapper' => 'table-wrapper',
        'event' => 'change',
      ],
    ];

    // 3. The Table (Updates via AJAX)
    $form['table_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'table-wrapper'],
    ];

    $selected_event_id = $form_state->getValue('filter_event');

    // Build the table headers
    $header = [
      'Name', 
      'Email', 
      'College', 
      'Department', 
      'Date',
      'Submission Time'
    ];

    $rows = [];
    if ($selected_date && $selected_event_id) {
      // Query registrations
      $query = $database->select('event_registration', 'r');
      $query->fields('r');
      $query->condition('event_date', $selected_date);
      $query->condition('event_id', $selected_event_id);
      $results = $query->execute()->fetchAll();

      foreach ($results as $row) {
        $rows[] = [
          $row->full_name,
          $row->email,
          $row->college_name,
          $row->department,
          $row->event_date,
          date('Y-m-d H:i:s', $row->created),
        ];
      }
      
      // Show total count
      $form['table_wrapper']['count'] = [
        '#markup' => '<h3>Total Participants: ' . count($rows) . '</h3>',
      ];
    }

    $form['table_wrapper']['registrations_table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found or select filters above.'),
    ];

    // 4. Export Button
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export to CSV'),
      '#name' => 'export_csv', // Specific name to detect click
    ];

    return $form;
  }

  // Ajax Callbacks
  public function updateEventDropdown(array &$form, FormStateInterface $form_state) {
    return $form['event_wrapper'];
  }

  public function updateTable(array &$form, FormStateInterface $form_state) {
    return $form['table_wrapper'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // This function handles the CSV Export
    $database = Database::getConnection();
    $date = $form_state->getValue('filter_date');
    $event_id = $form_state->getValue('filter_event');

    if (!$date || !$event_id) {
      $this->messenger()->addError('Please select both Date and Event to export.');
      return;
    }

    // Fetch data for CSV
    $query = $database->select('event_registration', 'r');
    $query->fields('r');
    $query->condition('event_date', $date);
    $query->condition('event_id', $event_id);
    $results = $query->execute()->fetchAll();

    // Generate CSV Content
    $csv_data = "Full Name,Email,College,Department,Event Name,Event Date\n";
    foreach ($results as $row) {
      $csv_data .= implode(',', [
        '"' . $row->full_name . '"',
        '"' . $row->email . '"',
        '"' . $row->college_name . '"',
        '"' . $row->department . '"',
        '"' . $row->event_name . '"',
        '"' . $row->event_date . '"',
      ]) . "\n";
    }

    // Trigger Download
    $response = new Response($csv_data);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="registrations.csv"');
    $form_state->setResponse($response);
  }
}