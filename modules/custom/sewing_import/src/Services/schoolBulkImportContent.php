<?php
namespace Drupal\sewing_import\Services;

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
    'name' => $item[57],
    'mail' => $item[16],
    'pass' => $item[58],
    'status' => 1,
  ));
  $user->set('field_first_name', $item[37]);
  $user->set('field_user_contact_no', $item[14]);
  $user->addRole(ROLE_SEWING_SCHOOL_ADMIN);
  $user -> save();

  $node = Node::create(['type' => 'sewing_school']);
  $node->set('title', $item[8]);
  $node->set('field_address_1', $item[10]);
  $node->set('field_address_2', $item[11]);
  $node->set('field_address_3', $item[12]);
  $node->set('field_sewing_affiliation_date', $item[19]);
  $node->set('field_affiliation_received_fees', $item[20]);
  $node->set('field_sewing_area_in_sqft', $item[17]);
  $node->set('field_area_of_operation', $item[24]);
  if(!empty($item[18])){
    $node->set('field_sewing_area_range', $item[18]);
  }

  $node->set('field_district', $item[54]);

  $node->set('field_sewing_financial_year', $item[0]);
  $node->set('field_sewing_grade', $item[9]);

  $node->set('field_location', $item[55]);

  $node->set('field_no_of_ss_machines', $item[26]);
  $node->set('field_no_of_courses', $item[34]);
  $node->set('field_sewing_no_of_teachers', $item[25]);
  $node->set('field_no_of_uj_machines', $item[27]);
  $node->set('field_sewing_pan_no', $item[23]);
  $node->set('field_sewing_phone_number', $item[15]);
  $node->set('field_pin_code', $item[13]);
  $node->set('field_sewing_date_of_renewal', $item[21]);
  $node->set('field_renewal_received_fees', $item[22]);


  $node->set('field_school_approval_date', $item[29]);
  $node->set('field_sew_school_approval_status', $item[33]);

  $node->set('field_sewing_school_code', $item[7]);
  $node->set('field_school_creation_date', $item[28]);
  $node->set('field_sewing_school_type', $item[6]);
  $node->set('field_sewing_select_dealer', $item[30]);
  $node->set('field_sewing_latitude', $item[35]);
  $node->set('field_sewing_longitude', $item[36]);

  $node->set('field_town_city', $item[1]);
 
  $node->set('field_sewing_user_id', $user->id());
  $node->status = 1;

  $node->enforceIsNew();
  $node->save();
} 