<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class schoolBulkImportContent {
  public static function schoolAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'School Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function schoolAddImportContentItemCallback($success, $results, $operations) {
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
  $user = User::create(array(
    'name' => $item[6],
    'mail' => $item[8],
    'pass' => $item[7],
    'status' => 1,
  ));
  $user->set('field_first_name', $item[4]);
  $user->set('field_user_contact_no', $item[9]);
  $user->addRole(ROLE_SILAI_SCHOOL_ADMIN);
  $user -> save();

  $node = Node::create(['type' => 'silai_school']);
  $node->set('field_school_type', $item[0]);
  $node->set('field_school_financial_year', $item[1]);
  $node->set('field_silai_business_state', $item[22]);
  $node->set('field_silai_district', $item[21]);
  $node->set('field_silai_block', $item[20]);
  $node->set('field_silai_village', $item[2]);
  $node->set('field_school_code', $item[3]);
  $node->set('field_name_of_ngo', $item[10]);
  $node->set('field_silai_learner_id', $item[11]);
  $node->set('title', $item[3]);
  $node->set('field_silai_teacher_age', $item[12]);
  $node->set('field_silai_teacher_education', $item[13]);
  $node->set('field_silai_school_address', $item[14]);
  $node->set('field_date_of_basic_training', $item[15]);
  $node->set('field_date_open_of_silai_school', $item[16]);
  $node->set('field_have_received_certificate', $item[17]);
  $node->set('field_silai_location', $item[23]);
  $node->set('field_sil_school_approval_status', $item[18]);
  $node->set('field_silai_teacher_user_id', $user->id());
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 