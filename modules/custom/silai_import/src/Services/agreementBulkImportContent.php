<?php
namespace Drupal\silai_import\Services; 

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class agreementBulkImportContent {
  public static function agreementAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Agreement Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function agreementAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'manage_agreements']);
  $node->set('title', 'Agreement-'.$item[8].'/'.$item[9]);
  $node->set('field_agreement_po_number', $item[0]);
  $node->set('field_agreement_po_date', $item[1]);
  $node->set('field_cararr_id', $item[2]);
  $node->set('field_agreement_ngo_name', $item[3]);
  $node->set('field_agreement_nfa_number', $item[4]);
  $node->set('field_agreement_amount', $item[5]);
  $node->set('field_silai_agre_received_amount', $item[6]);
  $node->set('field_silai_agree_due_balance', $item[7]);
  $node->set('field_contract_period_from_date', $item[8]);
  $node->set('field_contract_period_till_date', $item[9]);
  $node->set('field_agreement_no_of_schools', $item[10]);
  $schoolTypeDatas = explode(', ', $item[11]);
  foreach($schoolTypeDatas as $schoolTypeData){
    $schoolType[] = array_search($schoolTypeData, BULK_SCHOOL_TYPE_DATA);
  }
  $node->set('field_agreement_type_of_schools', $schoolType);
  $node->set('field_agreement_remarks', $item[12]);
  $node->set('field_received_payment_status', 0);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
  $agreementData = Node::load($node->id());
  if($item[6] != 0){
    $dataArray = array(
          'nid'               => $node->id(),
          'agreement_id'      => $agreementData->field_agreement_id->value,
          'cararr_id'         => $item[2],
          INSTALLMENT       => 1,
          PAYMENT_MODE      => 1,
          AMOUNT            => $item[6],
          'payment_status'    => 1,
      );
    $database = \Drupal::database();
    $insert_query = $database->insert(TABLE_SILAI_NGO_PAYMENT_DETAIL)->fields($dataArray)->execute(); 
  }
  
} 