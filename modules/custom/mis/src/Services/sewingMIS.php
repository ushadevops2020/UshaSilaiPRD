<?php
namespace Drupal\mis\Services;

use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;

class sewingMIS {

  public static function addImportContent($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $results = array();
    upload_sewing_weekly_mis($item);
    $context['results'][] = $item;
  }
  function addImportContentCallback($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One item processed.', '@count items successfully processed.'
      );
    } else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }

  /**
   * Method to get Week Drop Down
   * {@inheritdoc}
   */
  function getSewingWeeks($misId= ''){
      $current_user = \Drupal::currentUser();
      $roles = $current_user->getRoles();
      $user = User::load(\Drupal::currentUser()->id());
      $locationId = $user->field_user_location->target_id;

      $currdt= strtotime(WEEKLY_MIS_DROP_DOWN_DATE);
      $nextmonth=strtotime(WEEKLY_MIS_DROP_DOWN_DATE."+1 month");
	  if(date('N') > 5){
		  $currentWeek=strtotime(date()."0 week");
	  }else{
		  $currentWeek=strtotime(date()."-1 week");
	  }
      $i=0;
      do 
      {
        $weekday= date("w",$currdt);
        $nextday=7-$weekday;
        $endday=abs($weekday-7);
        $startarr[$i]=$currdt;
        $endarr[$i]=strtotime(date("Y-m-d",$currdt)."+$endday day");
        $currdt=strtotime(date("Y-m-d",$endarr[$i])."+1 day");
        $output[$startarr[$i].'@@'.$endarr[$i]] = date("d-M-y",$startarr[$i])." -- ". date("d-M-y",$endarr[$i]);
         $i++; 
                   
      }while($endarr[$i-1]<$currentWeek);
      $flag = 0;
      $conn = Database::getConnection();
      
      $query = $conn->select('usha_sewing_weekly_mis', 'm'); 
      $query->condition('is_deleted', 0);
      $query->condition('location', $locationId);
      $query->fields('m', array('week_start_date', 'week_end_date'));
      $record = $query->execute()->fetchAll();
      $record = json_decode(json_encode($record), true);
      if(!empty($misId)) {
        $select = $conn->select('usha_sewing_weekly_mis', 'm'); 
        $select->condition('id', $misId);
        $select->condition('is_deleted', 0); 
        $select->fields('m', array('week_start_date', 'week_end_date'));
        $result = $select->execute()->fetchAssoc();
        $key = array_search($result['week_start_date'], array_column($record, 'week_start_date'));
        unset($record[$key]);
      }
      foreach ($record as $key => $value) {
        $startDate = $value['week_start_date'];
        $endDate   = $value['week_end_date'];
        if(in_array($startDate.'@@'.$endDate, array_keys($output))) {
            unset($output[$startDate.'@@'.$endDate]);
        }
      }  
    return $output;
  }
}

// This function actually upload weekly each item.
function upload_sewing_weekly_mis($item) {
  $userId = \Drupal::currentUser()->id();
  $database = Database::getConnection(); 
  $i= 0; 
  $dataArr = array();
  $current_user = \Drupal::currentUser();
  $roles = $current_user->getRoles();
  $user = User::load($current_user->id());
  $nameWithRole = $user->get('field_first_name')->value.' '. $user->get('field_last_name')->value.' ('.$roles[1].')' ;
  $data = [
    'sender_role' => $roles[1],
    'receiver_id' => '',
    'receiver_role' => '',
    'message' => $message,
    'location' => $locationId,
    'created_by' => $current_user->id()
    ];
  foreach(SEWING_UPLOAD_WEEKLY_MIS_FIELDS as $key=>$value) {
      $dataArr['week_end_date'] = $item['endDate'];
      $dataArr['location'] = $item['location'];
      $dataArr[$value] = $item[$i];
      $dataArr['created_by'] = $userId;
      $dataArr['created_date'] = time();
      $weekStartDate = date('d/m/Y', $item[0]);
      $weekEndDate = date('d/m/Y', $item['endDate']);
      $data['message'] = "MIS for week ($weekStartDate - $weekEndDate) has been submitted by ".$nameWithRole;  
      $i++;
  }
  $database->insert('usha_sewing_weekly_mis')->fields($dataArr)->execute();
  $masterDataService = \Drupal::service('sewing.master_data');
  if(in_array($roles[1], [ROLE_SEWING_SSI])) {
      $masterSilaiDataService = \Drupal::service('silai.master_data');
      $targetUsers = $masterSilaiDataService->getUsersByRole(ROLE_SEWING_HO_ADMIN);
      if(!empty($targetUsers)){
        $masterDataService->sewingNotificationAlert($data, $targetUsers);
      }
  }
} 

