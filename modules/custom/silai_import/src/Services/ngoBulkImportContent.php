<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database; 
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class ngoBulkImportContent {
  public static function ngoAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'NGO Data Importing.. ' . $item;
    $results = array();
    create_ngo($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function ngoAddImportContentItemCallback($success, $results, $operations) {
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

function create_ngo($item) {
  $user = User::create(array(
    'name' => $item[6],
    'mail' => $item[4],
    'pass' => $item[7],
    'status' => $item[22],
  ));
  $user->set('field_first_name', $item[3]);
  $user->set('field_user_contact_no', $item[5]);
  $user->addRole(ROLE_SILAI_NGO_ADMIN);
  $user -> save();

  $node = Node::create(['type' => 'ngo']);
  $node->set('title', $item[0]);
  $node->set('field_partner_type', $item[1]);
  $node->set('field_ngo_location', $item[2]);
  $node->set('field_ngo_secondry_mobile_no', $item[8]);
  $node->set('field_ngo_vendor_code', $item[9]);
  $node->set('field_address_1', $item[10]);
  $node->set('field_address_2', $item[11]);
  $node->set('field_silai_pin_code', $item[12]);
  $node->set('field_pan_no', $item[13]);
  $node->set('field_account_no', $item[14]);
  $node->set('field_bank_name', $item[15]);
  $node->set('field_ifsc_code', $item[16]);
  $node->set('field_branch_address_1', $item[17]);
  $node->set('field_branch_address_2', $item[18]);
  $node->set('field_gstn_registered', $item[19]);
  $node->set('field_gstn_no', $item[20]);
  $node->set('field_ngo_remarks', $item[21]);
  $node->set('field_ngo_user_id', $user->id());
  $node->status = $item[22];
  $node->enforceIsNew();
  $node->save();

} 