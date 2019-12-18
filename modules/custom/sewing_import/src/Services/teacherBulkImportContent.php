<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class teacherBulkImportContent {
  public static function teacherImportContentItem($item, &$context){
    //print_r($item);die();
    $context['sandbox']['current_item'] = $item;
    $message = 'Teacher Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function teacherImportContentItemCallback($success, $results, $operations) {
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


function create_node($item) {
  $node = Node::create(['type' => 'sewing_teacher_management']);
  $node->set('field_sewing_school_code_list', $item[0]);
  $node->set('title', $item[1]);
  $node->set('field_sewing_teacher_gender', $item[2]);
  $node->set('field_sewing_teacher_email', $item[3]);
  $node->set('field_teacher_mobile_number', $item[4]);
  $node->set('field_sewing_teacher_phone_no', $item[5]);
  $node->set('field_sewing_teacher_address', $item[6]);
  $node->set('field_sewing_teacher_pin_code', $item[7]);
  $node->set('field_sewing_teacher_join_date', $item[8]);
  $node->set('field_sewing_teacher_pre_exp', $item[9]);
  $node->set('field_sewing_teacher_qualificati', $item[10]);
  $node->set('field_sewing_teacher_education', $item[11]);
  $node->set('field_teacher_certificate_issued', $item[12]);
  $node->set('field_certificate_issued_date', $item[13]);
  $node->set('field_sewing_teacher_certif_no', $item[14]);
  $node->set('field_sewing_teacher_training', $item[15]);
  $node->set('field_sewing_teacher_trained_by', $item[16]);
  $node->set('field_sewing_teacher_subject', $item[17]);
  $node->set('field_sewing_teacher_tran_date', $item[18]);
  $node->set('field_sewing_teacher_skill_level', $item[19]);
  $node->set('field_sewing_teacher_status', $item[20]);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 