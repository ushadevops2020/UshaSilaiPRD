<?php
namespace Drupal\silai\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class learnerBulkImportContent {
  public static function learnerAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Learner Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function learnerAddImportContentItemCallback($success, $results, $operations) {
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
    $node = Node::create(['type' => 'silai_learners_manage']);
    $node->set('field_silai_school', $item[0]);
    $node->set('title', $item[1]);
    $node->set('field_age', $item[2]);
    $node->set('field_silai_date_of_enrollment', $item[3]);
    //$node->set('field_address_1', $item[4]);
    $node->set('field_address_2', $item[4]);
    $node->set('field_pin_code', $item[5]);
    $node->set('field_educational_qualification', $item[6]);
    $node->set('field_marital_status', $item[7]);
    $node->set('field_father_husband_name', $item[8]);
    $node->set('field_occupation_of_father_husba', $item[9]);
    $node->set('field_mother_name', $item[10]);
    $node->set('field_occupation_of_mother', $item[11]);

    $node->set('field_monthly_income_of_househol', $item[12]);
    $node->set('field_male_members_in_household', $item[13]);
    $node->set('field_female_member_in_household', $item[14]);
    $node->set('field_mobile_number', $item[15]);
    $node->set('field_landline_number', $item[16]);
    $node->set('field_sewing_machine_at_home', $item[17]);
    $node->set('field_association_social_group', $item[18]);
    $node->set('field_silai_course_code', $item[19]);
    $node->set('field_monthly_fee_for_course', $item[20]);
    $node->set('field_do_after_learning_school', $item[21]);
    $node->set('field_course_completion_date', $item[22]);
    $node->set('field_you_received_certificate', $item[23]);
    //$node->set('field_course_completed', $item[25]);
    #
    $node->set('field_silai_district', $item[25]);
    //$node->set('field_silai_block', $item[26]);
    $node->status = 1;
    $node->enforceIsNew();
    $node->save();
} 