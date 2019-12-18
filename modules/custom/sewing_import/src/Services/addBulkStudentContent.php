<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class addBulkStudentContent {
  public static function studentAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Student Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function studentAddImportContentItemCallback($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One item processed.', '@count items successfully processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}

// This function actually creates each item as a node as type 'Page'
function create_node($item) {
  $node = Node::create(['type' => 'manage_sewing_students']);
  $node->set('field_location', $item['locationNid']);
  $node->set('field_sewing_school_code_list', $item[0]);
  $node->set('field_school_name_sewing', $item['schoolName']);
  $node->set('field_school_type', $item['schoolTypeNid']);
  $node->set('field_student_salutation', $item[1]);
  $node->set('title', $item[2]);
  $node->set('field_last_name', $item[3]);
  $node->set('field_sewing_father_name', $item[4]);
  $node->set('field_sewing_gender', $item[5]);
  $node->set('field_sewing_date_of_birth', $item[6]);
  $node->set('field_sewing_qualification', $item[7]);
  $node->set('field_address_1', $item[8]);
  $node->set('field_address_2', $item[9]);
  $node->set('field_business_state', $item[10]);
  $node->set('field_pin_code', $item[11]);
  $node->set('field_sewing_mobile_no', $item[12]);
  $node->set('field_sewing_phone_no', $item[13]);
  $node->set('field_sewing_email_address', $item[14]);
  $node->set('field_sewing_course_start_date', $item[15]);
  $node->set('field_student_admission_date', $item['admissionDate']);
  $node->set('field_sewing_course_code_list', $item[16]);
  $node->set('field_sewing_course_name', $item['courseCodeName']);
  $node->set('field_sewing_course_duration', $item['courseDuration']);
  $node->set('field_sew_course_completion_date', $item['courseCompleteDate']);
  $node->set('field_sewing_course_fee', $item['courseFee']);
  $node->set('field_sewing_course_fee_due', $item['feeDue']);
  $node->set('field_sewing_course_fee_received', $item['feeRecived']);
  $node->set('field_sewing_course_fee_out', $item['outstandingfee']);
  $node->set('field_existing_sewing_machine_br', $item[17]);
  $node->set('field_sewing_model_of_sm', $item[18]);
  $node->set('field_sewing_want_to_buy_new', $item[19]);
  $node->set('field_sewing_model_make', $item[20]);
  $node->set('field_sewing_time_to_buy', $item[21]);
  $node->set('field_future_plan_after_course', $item[22]);
  $node->set('field_student_status', 1);
  $node->set('status', 1);
  $node->enforceIsNew();
  $node->save();
} 