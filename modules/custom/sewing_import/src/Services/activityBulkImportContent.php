<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class activityBulkImportContent {
  public static function activityAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Workshop-Activity Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  
  function activityAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'training']);
  $node->set('title', 'Node Sewing Training' );
  $node->set('field_training_date', $item[0]);
  $node->set('field_sewing_training_subject', $item[1]);
  $node->set('field_training_town', $item[2]);
  $node->set('field_venue', $item[3]);
  $node->set('field_sewing_school_name', $item[4]);
  $node->set('field_sm_representative_name', $item[5]);
  $node->set('field_sm_representative_designat', $item[6]);
  $node->set('field_no_of_attendees', $item[7]);
  $node->set('field_training_pros_gen_for_sch', $item[8]);
  $node->set('field_training_prosp_gen_for_sal', $item[9]);
  $node->set('field_training_conf_no_of_sch_ad', $item[10]);
  $node->set('field_training_conf_no_of_sale', $item[11]);
  $node->set('field_training_trainer_name', $item[12]);
  $node->set('field_training_remarks', $item[13]); 
  $node->set('field_training_type', 'Activity');
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 