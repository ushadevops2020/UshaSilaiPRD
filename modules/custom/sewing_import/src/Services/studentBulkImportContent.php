<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class studentBulkImportContent {
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
  $node->set('title', $item[10]);
  $node->set('field_address_1', $item[15]);
  $node->set('field_address_2', $item[16]);
  $node->set('field_address_3', $item[17]);
  $node->set('field_student_admission_date', $item[8]);
  $node->set('field_student_admission_no', $item[9]);
  if(!empty($item[37])){
    $node->set('field_sewing_certificate_issued', $item[37]);
  }

  $node->set('field_sewing_course_code_list', $item[25]);
  $node->set('field_sewing_course_start_date', $item[27]);
  $node->set('field_sewing_course_type', $item[24]);
  $node->set('field_sewing_date_of_birth', $item[13]);
  $node->set('field_sewing_district_selectlist', $item[52]);
  $node->set('field_sewing_email_address', $item[21]);
  $node->set('field_sewing_exam_appearance', $item[32]);
  $node->set('field_sewing_exam_result', $item[33]);
  $node->set('field_existing_sewing_machine_br', $item[39]);
  if(!empty($item[47]) && $item[47] == 0){
    $node->set('field_sewing_exit_code', $item[48]);
  }
  $node->set('field_sewing_father_name', $item[11]);
  $node->set('field_sewing_course_fee_due', $item[29]);
  $node->set('field_sewing_course_fee_received', $item[30]);
  $node->set('field_sewing_financial_year', $item[50]);
  $node->set('field_future_plan_after_course', $item[45]);
  $node->set('field_sewing_gender', $item[12]);
  $node->set('field_sewing_grades', $item[35]);
  $node->set('field_location', $item[53]);

  $node->set('field_sewing_marital_status', $item[14]);
  $node->set('field_sewing_mobile_no', $item[19]);
  $node->set('field_sewing_model_make', $item[42]);
  $node->set('field_sewing_model_of_sm', $item[40]);
  $node->set('field_sewing_course_fee_out', $item[31]);
  $node->set('field_sewing_phone_no', $item[20]);
  $node->set('field_pin_code', $item[18]);
  $node->set('field_sewing_qualification', $item[22]);
  $node->set('field_sewing_result_date', $item[34]);
  $node->set('field_sewing_school_code_list', $item[6]);
  $node->set('field_school_name_sewing', $item[54]);
  $node->set('field_school_type', $item[55]);

  if(!empty($item[47])){
    $node->set('field_student_status', $item[47]);
  }
  if(!empty($item[43]) && ($item[43] == 1 ||$item[43] == 2 ||$item[43] == 3 ||$item[43] == 4 ||$item[43] == 6 ||$item[43] == 9)){
    $node->set('field_sewing_time_to_buy', $item[43]);
  }
  $node->set('field_town_city', $item[51]);
  $node->set('field_sewing_want_to_buy_new', $item[41]);
  if($item[37] != 1){
    $node->status = 1;
  }
  $node->enforceIsNew();
  $node->save();
} 