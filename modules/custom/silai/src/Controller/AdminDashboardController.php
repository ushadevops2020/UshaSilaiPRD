<?php

namespace Drupal\silai\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Database\Database;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * AdminDashboardController class.
 */
class AdminDashboardController extends ControllerBase {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function dashboardPage() {
    $masterDataService = \Drupal::service('silai.master_data');
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    /*
     * Total no of Active Schools Count.
     */

    $SchoolCount = $this->schoolIdsForSchoolChartCount();
    $SchoolCount = number_format($SchoolCount);
    /*
     * Agreement Amount data.
    */
    $pendingAgreementamount = $this->pendingAmountForAgreement();
    $pendingAgreementamount = number_format($pendingAgreementamount);
    /*
     * No of Active Learners count.
    */

    $learnerIds = $this->learnerIdsForLearnerChart();
    $noOfActiveLearners = count($learnerIds);
    $noOfActiveLearners = number_format($noOfActiveLearners);

    #Notice Board View Block. 
    $viewReceiveMsg = Views::getView('send_message_list');
    $viewReceiveMsg->setDisplay('block_1');
    $viewReceiveMsgRender = $viewReceiveMsg->render();

    $viewSendMsg = Views::getView('send_message_list');
    $viewSendMsg->setDisplay('block_2'); 
    $viewSendMsgRender = $viewSendMsg->render();

    #Gallery Slider
    if($roles[1] == 'pc'){
      $user = User::load($current_user->id());
      $locationId = $user->field_user_location->target_id; 
      $viewSliderGallery = Views::getView('gallery_view');
      $viewSliderGallery->setDisplay('block_2');
      $viewSliderGallery->setArguments(array($locationId));
      $viewSliderGalleryRender = $viewSliderGallery->render();
    }else if($roles[1] == 'ngo_admin'){
      $currentUserid = $current_user->id();
      $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
     
      $viewSliderGallery = Views::getView('gallery_view');
      $viewSliderGallery->setDisplay('block_2');
      $viewSliderGallery->setArguments($getNgoLocationIds);
      $viewSliderGalleryRender = $viewSliderGallery->render();
    }else{
      $viewSliderGallery = Views::getView('gallery_view');
      $viewSliderGallery->setDisplay('block_1');
      $viewSliderGalleryRender = $viewSliderGallery->render();
    }
    #Pending MIS DAta

    #### New MIS Data
    $locationID = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'manage_silai_locations')
      ->execute();
    $locationCount = count($locationID);
    $schoolForMisCount = $this->schoolIdsForSchoolChartCount();
    $fixedYear = '2019';
    $fixedFYear = '2019-2020';
    $fixedDate = strtotime('1-Apr-2019');
    $currentYear = date('Y');
    $currentMonth = date('n');
    if($currentMonth <=3){
      $lastYear = $currentYear-1;
      $fYear = $lastYear.'-'.$currentYear;
    }else if($currentMonth >= 4){
      $nextYear = $currentYear+1;
      $fYear = $currentYear.'-'.$nextYear;
    }
    $fYear = explode('-', $fYear);
    $yearCount = $fYear[0] - $fixedYear + 1;
    $totalWeeklyMISData = $locationCount * $yearCount * 52;
    $totalMonthMisData = $schoolForMisCount * $yearCount * 12;
    $totalQuarterlyMisData = $schoolForMisCount * $yearCount * 4;
    #
    if($roles[1] == ROLE_SILAI_PC){
      $user = User::load($current_user->id());
      $locationId = $user->field_user_location->target_id;
      $totalMisData = $totalMonthMisData + $totalQuarterlyMisData;
      #
      $conn = Database::getConnection();
      $query = $conn->select('usha_monthly_mis', 's');
      $query->condition('fiscal_year', $fixedFYear, '>=');
      $query->condition('monthly_quarterly_type', 0);
      $query->condition('location', $locationId);
      $query->fields('s');
      $submitMonthlyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitMonthlyMISData = count($submitMonthlyMIS);
      #
      $query = $conn->select('usha_monthly_mis', 's');
      $query->condition('fiscal_year', $fixedFYear, '>=');
      $query->condition('monthly_quarterly_type', 1);
      $query->condition('location', $locationId);
      $query->fields('s');
      $submitQuarterlyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitQuarterlyMISData = count($submitQuarterlyMIS);
      $totalSubmitMisData = $submitMonthlyMISData + $submitQuarterlyMISData;

    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      $totalMisData = $totalMonthMisData + $totalQuarterlyMisData;
      $currentUserid = $current_user->id();
      #
      $conn = Database::getConnection();
      $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
      $query->condition('field_ngo_user_id', $currentUserid);
      $ngoId = $query->execute();
      $ngoId = array_values($ngoId);
      #
      $query = $conn->select('usha_monthly_mis', 's');
      $query->condition('fiscal_year', $fixedFYear, '>=');
      $query->condition('monthly_quarterly_type', 0);
      $query->condition('ngo_id', $ngoId[0]);
      $query->fields('s');
      $submitMonthlyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitMonthlyMISData = count($submitMonthlyMIS);
      #
      $query = $conn->select('usha_monthly_mis', 's');
      $query->condition('fiscal_year', $fixedFYear, '>=');
      $query->condition('monthly_quarterly_type', 1);
      $query->condition('ngo_id', $ngoId[0]);
      $query->fields('s');
      $submitQuarterlyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitQuarterlyMISData = count($submitQuarterlyMIS);
      $totalSubmitMisData = $submitMonthlyMISData + $submitQuarterlyMISData;
    }else{
      $conn = Database::getConnection();
      $totalMisData = $totalWeeklyMISData + $totalMonthMisData + $totalQuarterlyMisData;
      $query = $conn->select('usha_weekly_mis', 's');
      $query->condition('week_start_date', $fixedDate, '>=');
      $query->fields('s');
      #
      $submitWeeklyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitWeeklyMISData = count($submitWeeklyMIS);
      #
      $query = $conn->select('usha_monthly_mis', 's');
      $query->condition('fiscal_year', $fixedFYear, '>=');
      $query->condition('monthly_quarterly_type', 0);
      $query->fields('s');
      $submitMonthlyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitMonthlyMISData = count($submitMonthlyMIS);
      #
      $query = $conn->select('usha_monthly_mis', 's');
      $query->condition('fiscal_year', $fixedFYear, '>=');
      $query->condition('monthly_quarterly_type', 1);
      $query->fields('s');
      $submitQuarterlyMIS = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $submitQuarterlyMISData = count($submitQuarterlyMIS);
      $totalSubmitMisData = $submitWeeklyMISData + $submitMonthlyMISData + $submitQuarterlyMISData;
    }
    $totalPendingMIS = $totalMisData - $totalSubmitMisData;
    $totalPendingMIS = number_format($totalPendingMIS);

    #School Type Data Info For School Chart
    $schoolTypeIds = \Drupal::entityQuery(NODE)->condition(TYPE, 'silai_school_type_master')->execute();
    foreach ($schoolTypeIds as $schoolTypeId) {
      $schoolTypeNode = Node::load($schoolTypeId);
      $outputScoolType .= '"'.$schoolTypeNode->field_silai_school_type_code->value.':- '.$schoolTypeNode->getTitle().'",  ';
    }
    /** Total Village Count**/

    $query = $conn->select('node__field_silai_village', 'nfsv');
      $query->distinct();
      $query->condition('bundle', 'silai_school');
      $query->fields('nfsv');
      $query->groupBy('nfsv.field_silai_village_target_id');
      $villages = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      
      $totalVillage = number_format(count($villages));

    // $villageIds = \Drupal::entityQuery(NODE)
    //   ->condition(TYPE, 'node__field_silai_village')
    //   ->condition('bundle', 'silai_school')
    //   ->execute();
    // $totalVillage = number_format(count($villageIds));
    /** Associated NGO Partners Count**/
    $assocNGOPartnersIds = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'ngo')
      ->condition('field_partner_type', 19)
      ->condition('status', 1)
      ->execute();
    $totalNgoPartners = number_format(count($assocNGOPartnersIds));
    return [
            '#title' => 'Dashboard',
            '#theme' => 'ho_admin_dashboard_page',
            '#role' => $roles[1],
            '#SchoolCount' => $SchoolCount,
            '#pendingAgreementamount' => $pendingAgreementamount,
            '#noOfActiveLearners' => $noOfActiveLearners,
            '#viewReceiveMsgRender' => $viewReceiveMsgRender,
            '#viewSendMsgRender' => $viewSendMsgRender,
            '#viewSliderGalleryRender' => $viewSliderGalleryRender,
            '#totalPendingMIS' => $totalPendingMIS,
            '#outputScoolType' => $outputScoolType,
            '#totalVillage' => $totalVillage,
            '#totalNgoPartners' => $totalNgoPartners,
        ];
  }
  #School Chart data and filter
  public function hoadminSchoolChartLoad() { 
    $filterField = \Drupal::request()->request; 
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $currentUserid = $current_user->id();
    $yearFilter = $filterField->get('yearFilter');
    $locationId = $filterField->get('location_id');
    if($roles[1] == ROLE_SILAI_PC){
      if(empty($locationId)){
        $user = User::load($current_user->id());
        $locationId = $user->field_user_location->target_id;
      }
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      if(empty($locationId)){
        $masterDataService = \Drupal::service('silai.master_data');
        $currentUserid = $current_user->id();
        $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
        $locationId = $getNgoLocationIds[0];
        // $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
        // $query->condition('field_ngo_user_id', $currentUserid);
        // $ngoId = $query->execute();
        $ngoId = $masterDataService->getLinkedNgoForUser($currentUserid);
      }else{
        $currentUserid = $current_user->id();
        $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
        $query->condition('field_ngo_user_id', $currentUserid);
        $ngoId = $query->execute();
      }
    }
    //$yearFilter = 2019;
    $schoolTypeIds = \Drupal::entityQuery(NODE)->condition(TYPE, 'silai_school_type_master')->execute();
    $schoolTypeNodes = \Drupal\node\Entity\Node::loadMultiple($schoolTypeIds);
    $schoolChartData[0] = ['School Type', 'No. of Schools:'];
    $i= 1;
    foreach($schoolTypeNodes as $schoolTypeNode) {
      $countSchoolArray1 = 0;
      $countSchoolArray2 = 0;
      if($locationId){
        if($yearFilter){
            $query = \Drupal::entityQuery(NODE)->condition(TYPE, SILAI_SCHOOL)->condition('field_sil_school_approval_status', 1);
            $query->condition('field_school_type', $schoolTypeNode->id());
            $query->condition('field_silai_location', $locationId);
            if($roles[1] == ROLE_SILAI_NGO_ADMIN){
              $query->condition('field_name_of_ngo', $ngoId);
            }
            $schoolIds = $query->execute();
            $schoolNodes = \Drupal\node\Entity\Node::loadMultiple($schoolIds);
            foreach ($schoolNodes as $schoolNode) {
              $termData = Term::load($schoolNode->field_school_financial_year->target_id);
              $termName = $termData->name->value;
              if($termName == $yearFilter){
                 $countSchoolArray1++;
              }
          }
        }else{
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, SILAI_SCHOOL)->condition('field_sil_school_approval_status', 1);
          $query->condition('field_school_type', $schoolTypeNode->id());
          $query->condition('field_silai_location', $locationId);
          if($roles[1] == ROLE_SILAI_NGO_ADMIN){
            $query->condition('field_name_of_ngo', $ngoId);
          }
          $schoolIds = $query->execute();
        }
      }else{
        if($yearFilter){
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, SILAI_SCHOOL)->condition('field_sil_school_approval_status', 1);
          $query->condition('field_school_type', $schoolTypeNode->id());
          if($roles[1] == ROLE_SILAI_NGO_ADMIN){
            $query->condition('field_name_of_ngo', $ngoId);
          }
          $schoolIds = $query->execute();
          $schoolNodes = \Drupal\node\Entity\Node::loadMultiple($schoolIds);
          foreach ($schoolNodes as $schoolNode) {
            $termData = Term::load($schoolNode->field_school_financial_year->target_id);
            $termName = $termData->name->value;
            if($termName == $yearFilter){
               $countSchoolArray2++;
            }
          }
        }else{
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, SILAI_SCHOOL)->condition('field_sil_school_approval_status', 1);
          $query->condition('field_school_type', $schoolTypeNode->id());
          if($roles[1] == ROLE_SILAI_NGO_ADMIN){
            $query->condition('field_name_of_ngo', $ngoId);
          }
          $schoolIds = $query->execute();
        }
      }
      //$schoolTypeTitle = $schoolTypeNode->getTitle();
      $schoolTypeTitle = $schoolTypeNode->field_silai_school_type_code->value;
      $schoolCount = count($schoolIds);
      //$schoolChartData[$i][] = $schoolTypeTitle; 
      if($locationId){
        if($yearFilter){
          $schoolChartData[$i][] = $schoolTypeTitle.' ('.$countSchoolArray1.')';
          $schoolChartData[$i][] = $countSchoolArray1;
        }else{
          $schoolChartData[$i][] = $schoolTypeTitle.' ('.$schoolCount.')';
          $schoolChartData[$i][] = $schoolCount;
        }
      }else{
        if($yearFilter){
          $schoolChartData[$i][] = $schoolTypeTitle.' ('.$countSchoolArray2.')';
          $schoolChartData[$i][] = $countSchoolArray2;
        }else{
          $schoolChartData[$i][] = $schoolTypeTitle.' ('.$schoolCount.')';
          $schoolChartData[$i][] = $schoolCount;
        }
      }
      $i++;
    }
    $totalSchool = 0;
    foreach ($schoolChartData as $schoolData) {
      if(is_numeric($schoolData[1])){
        $totalSchool = $totalSchool + $schoolData[1];
      }
    }
    $result['chartData'] = $schoolChartData;
    $result['rawData'] = number_format($totalSchool);
    return new JsonResponse($result);
  }
  #school filter options by location
  public function getLocationFilterOptions(){
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      if(empty($locationId)){
        $user = User::load($current_user->id());
        $locationId = $user->field_user_location->target_id;
        $nodeData = Node::load($locationId);
        $locations[$locationId] = $nodeData->getTitle();
      }
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      $masterDataService = \Drupal::service('silai.master_data');
      $currentUserid = $current_user->id();
      $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
      //print_r($getNgoLocationIds);
      foreach ($getNgoLocationIds as $getNgoLocationId) {
        $nodeData = Node::load($getNgoLocationId);
        $locations[$getNgoLocationId] = $nodeData->getTitle();
      }
    }else{
      $locationIds = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'manage_silai_locations')
      ->condition('status', 1)
      ->sort('title')
      ->execute();
      $locationsNodes = \Drupal\node\Entity\Node::loadMultiple($locationIds);
      foreach($locationsNodes as $locationsNode) {
        $locations['"'.$locationsNode->id().'"'] = $locationsNode->getTitle();
      }
    }

    $return = ['data' => $locations, STATUS => 1];    
    return new JsonResponse($return);
  }
  #Month Filter Options
  public function getMonthFilterOptions(){
    $monthData = DASHBOARD_MONTH_FILTER;
    $currentMonth = date('n');
    $i = 1;
    foreach ($monthData as $month) {
      if($currentMonth >= $i){
        $monthList[$i] = $month;
        $i++;
      }
    }
    //$monthList  = array_reverse($monthList);
    $return = ['data' => $monthList, STATUS => 1];    
    return new JsonResponse($return);
  }
  # Agreement Chart data and filter
  public function hoadminAgreementChartLoad(){
    $filterField = \Drupal::request()->request;  
    $locationId = $filterField->get('location_id');
    $monthArray = DASHBOARD_MONTH_FILTER;
    $monthKey = $filterField->get('monthData');
    $monthFilter = $monthArray[$monthKey];
    //print_r($monthFilter);
    if($monthKey){
      if($monthFilter == date('M')){
        $monthData = date('d').'-'.$monthFilter.'-'.date('Y');
      }else{
        if($monthFilter == 'Jan' || $monthFilter == 'Mar' || $monthFilter == 'May'|| $monthFilter == 'Jul' || $monthFilter == 'Aug'|| $monthFilter == 'Oct'|| $monthFilter == 'Dec'){
          $monthData = '31-'.$monthFilter.'-'.date('Y');
        }else if($monthFilter == 'Apr' || $monthFilter == 'Jun' || $monthFilter == 'Sep'){
          $monthData = '30-'.$monthFilter.'-'.date('Y');
        }else{
          $year = date('Y');
          $leap = date('L', mktime(0, 0, 0, 1, 1, $year));
          if($leap){
            $monthData = '29-'.$monthFilter.'-'.date('Y');
          }else{
            $monthData = '28-'.$monthFilter.'-'.date('Y');
          }
        }
        
      }
    }
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      if(empty($locationId)){
        $user = User::load($current_user->id());
        $locationId = $user->field_user_location->target_id;
      }
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      if(empty($locationId)){
        $masterDataService = \Drupal::service('silai.master_data');
        $currentUserid = $current_user->id();
        $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
        $locationId = $getNgoLocationIds[0];
      }
    }
    if($locationId){
      $agreementDatas = $this->agreementQueryByLocation($locationId);
        if($monthData){
          $dueAgreementamountY = 0;
          $paidAgreementamountY = 0;
          foreach ($agreementDatas as $agreementData) {
            //$agreementDateYear = date('d-M-Y', $agreementData->created);
            if(strtotime($monthData) >= $agreementData->created){
              $dueAgreementamountY = $dueAgreementamountY + $agreementData->field_silai_agree_due_balance_value;
              $paidAgreementamountY = $paidAgreementamountY + $agreementData->field_silai_agre_received_amount_value;
            }
          }
        }else{
          $dueAgreementamount = 0;
          $paidAgreementamount = 0;
          foreach ($agreementDatas as $agreementData) {
            $dueAgreementamount = $dueAgreementamount + $agreementData->field_silai_agree_due_balance_value;
            $paidAgreementamount = $paidAgreementamount + $agreementData->field_silai_agre_received_amount_value;
          }
        }
    }else{
      $agreementIds = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'manage_agreements')
        ->execute();
      $agreementNodes = \Drupal\node\Entity\Node::loadMultiple($agreementIds);
      $dueAgreementamount = 0; $paidAgreementamount = 0;
      $dueAgreementamountY = 0; $paidAgreementamountY = 0;
      foreach($agreementNodes as $agreementNode) {
        if($monthData){
          if(strtotime($monthData) >= $agreementNode->created->value){
            //print_r(date('d-M-Y', $agreementNode->created->value));
            $dueAgreementamountY = $dueAgreementamountY + $agreementNode->field_silai_agree_due_balance->value;
            $paidAgreementamountY = $paidAgreementamountY + $agreementNode->field_silai_agre_received_amount->value;
          }
        }else{
          $dueAgreementamount = $dueAgreementamount + $agreementNode->field_silai_agree_due_balance->value;
          $paidAgreementamount = $paidAgreementamount + $agreementNode->field_silai_agre_received_amount->value;
        }
        
      }
    }
    if($monthData){
      if($dueAgreementamountY != 0 && $paidAgreementamountY != 0){
         $returnData = [
            ['Title', 'Amount'],
            ['Due Amount- Rs.'.number_format($dueAgreementamountY), $dueAgreementamountY],
            ['Paid Amount- Rs.'.number_format($paidAgreementamountY), $paidAgreementamountY]
          ];
      }else{
        $returnData = 'No Data Found.';
      }
    }else{
      if($dueAgreementamount != 0 && $paidAgreementamount != 0){
         $returnData = [
            ['Title', 'Amount'],
            ['Due Amount- Rs.'.number_format($dueAgreementamount), $dueAgreementamount],
            ['Paid Amount- Rs.'.number_format($paidAgreementamount), $paidAgreementamount]
          ];
      }else{
        $returnData = 'No Data Found.';
      }
    }
    //$result['chartData'] = $returnData;
    //$result['dueAmount'] = $dueAgreementamount;
    //$result['paidAmount'] = $paidAgreementamount;
    return new JsonResponse($returnData);
  }
  #Learner Chart data and Filter
  public function hoadminLearnerChartLoad(){
    $filterField = \Drupal::request()->request;  
    $learnerTime = $filterField->get('learner_time');
    $locationID = $filterField->get('location_id');

    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      if(empty($locationID)){
        $user = User::load($current_user->id());
        $locationID = $user->field_user_location->target_id;
      }
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      if(empty($locationID)){
        $masterDataService = \Drupal::service('silai.master_data');
        $currentUserid = $current_user->id();
        $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
        $locationID = $getNgoLocationIds[0];
      }
    }
    
    $monthEnrollmentArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    $monthCompletionArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    
    $getEnrolledLearnerCount = $this->getLearnerCount($learnerTime, $locationID, 'field_silai_date_of_enrollment_value');
    foreach ($getEnrolledLearnerCount as $key => $value) {
      $monthEnrollmentArray[$value->month] = (int)$value->count;
    }

    $getCompletionLearnerCount = $this->getLearnerCount($learnerTime, $locationID, 'field_course_completion_date_value');
    foreach ($getCompletionLearnerCount as $key => $value) {
      $monthCompletionArray[$value->month] = (int)$value->count;
    }
    # Preparing Chart Array
    $totalEnrolled = 0;
    $courseCompleted = 0;
    foreach ($monthEnrollmentArray as $key => $value) {
      $arrDatas[] = [$key, $value, $monthCompletionArray[$key]];
      $totalEnrolled =  $totalEnrolled + $value;
      $courseCompleted =  $courseCompleted + $monthCompletionArray[$key];
    }
    $a = 1;
    $learnerChartData[0] = ['Month', 'Total Enrolled- '.$totalEnrolled, 'Course Completed- '.$courseCompleted];
    foreach ($arrDatas as $arrData) {
      $learnerChartData[$a][] = $arrData[0];
      $learnerChartData[$a][] =  $arrData[1];
      $learnerChartData[$a][] =  $arrData[2];
      $a++;
    }

    //return new JsonResponse($learnerChartData);
	$result['chart'] = $learnerChartData;
	$result['count'] = number_format($totalEnrolled);

    if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      $currentUserid = $current_user->id();
      $learnerIds = $masterDataService->getAllLearnerAssWithNgo($currentUserid);
      $result['count'] = count($learnerIds);
    }


	
    $response = new CacheableJsonResponse($result);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($result));
    return $response;
  }
  #MIS Weekly Time Filter Options
  public function hoadminMISWeeklyTimeFilter(){
    $optionList = CHART_MIS_WEEKLY_FILTER_OPTION;
    $return = ['data' => $optionList, STATUS => 1];    
    return new JsonResponse($return);
  }
  #MIS Monthly Time Filter Options
  public function hoadminMISMonthlyTimeFilter(){
    $optionList = CHART_MIS_MONTHLY_FILTER_OPTION;
    $return = ['data' => $optionList, STATUS => 1];    
    return new JsonResponse($return);
  }
  #MIS Quarterly Time Filter Options
  public function hoadminMISQuarterlyTimeFilter(){
    $optionList = CHART_MIS_QUARTERLY_FILTER_OPTION;
    $return = ['data' => $optionList, STATUS => 1];    
    return new JsonResponse($return);
  }
  #Year Filter Options
  // public function hoadminYearFilterOptions(){
  //   for($currentYear = date('Y'); $currentYear >= 2011; $currentYear--){
  //     $yearData[$currentYear] = $currentYear;
  //   }
  //   //$yearData = rsort($yearData);
  //   $return = ['data' => $yearData, STATUS => 1];    
  //   //return new JsonResponse($return);
  //   return new JsonResponse($return);
  // }
  #Financial Year Filter Options
  public function hoadminFinancialYearFilterOptions(){
    $currentYear = date('Y');
    $currentMonth = date('n');
    if($currentMonth <=3){
      $lastYear = $currentYear-1;
      $fYear = $lastYear.'-'.$currentYear;
    }else if($currentMonth >= 4){
      $nextYear = $currentYear+1;
      $fYear = $currentYear.'-'.$nextYear;
    }
    $yearData[0] = $fYear;
    $fYear = explode('-', $fYear);
    $a = 1;
    for($i = $fYear[0]; $i>= 2011; $i--){
      $year = ($i-1).'-'.$i;
      $yearData[$a] = $year;
      $a++;
    }
    $return = ['data' => $yearData, STATUS => 1]; 
    return new JsonResponse($return);
  }
  # MIS Chart Array
  public function hoadminMisChartLoad(){
    $filterField = \Drupal::request()->request; 
    $typeFilter = $filterField->get('misTypeFilter');
    $timeFilter = $filterField->get('misTimeFilter');
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $conn = Database::getConnection();
    if(empty($typeFilter)){
      if($roles[1] == ROLE_SILAI_PC){
        $typeFilter = 'monthly';
      }else if( $roles[1] == ROLE_SILAI_NGO_ADMIN){
        $typeFilter = 'monthly';
      }else {
        $typeFilter = 'weekly';
      }
    }
    if(empty($timeFilter)){
      $timeFilter = 1;
    }
    //$typeFilter = 'quarterly';
    //$timeFilter = 1;
    $locationID = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'manage_silai_locations')
      ->execute();
    if($typeFilter == 'weekly'){
      if($timeFilter == 1){
        $submitAndPendingMIS = $this->getWeeklyMisPendingAndSubmitData(date('W')-2, $locationID);
        $submitMIS = $submitAndPendingMIS['submitMIS'];
        $pendingMIS = $submitAndPendingMIS['pendingMIS'];
      }else if($timeFilter == 2){
        $submitAndPendingMIS = $this->getWeeklyMisPendingAndSubmitData(date('W')-3, $locationID);
        $submitMIS = $submitAndPendingMIS['submitMIS'];
        $pendingMIS = $submitAndPendingMIS['pendingMIS'];
      }else if($timeFilter == 3){
        $submitAndPendingMIS = $this->getWeeklyMisPendingAndSubmitData(date('W')-4, $locationID);
        $submitMIS = $submitAndPendingMIS['submitMIS'];
        $pendingMIS = $submitAndPendingMIS['pendingMIS']; 
      }
    }
    if($typeFilter == 'monthly'){
      if($timeFilter == 1){

        $submitAndPendingMIS = $this->getMonthlyMisPendingAndSubmitData(date('n')-1);
        $submitMIS = $submitAndPendingMIS['submitMIS'];
        $pendingMIS = $submitAndPendingMIS['pendingMIS'];
  
      }else if($timeFilter == 2){
        $submitAndPendingMIS = $this->getMonthlyMisPendingAndSubmitData(date('n')-2);
        $submitMIS = $submitAndPendingMIS['submitMIS'];
        $pendingMIS = $submitAndPendingMIS['pendingMIS'];

      }else if($timeFilter == 3){

        $submitAndPendingMIS = $this->getMonthlyMisPendingAndSubmitData(date('n')-3);
        $submitMIS = $submitAndPendingMIS['submitMIS'];
        $pendingMIS = $submitAndPendingMIS['pendingMIS'];

      }
    }
    if($typeFilter == 'quarterly'){
      if($timeFilter == 1){
        #current FYear
        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth <=3){
          $lastYear = $currentYear-1;
          $fYear = $lastYear.'-'.$currentYear;
        }else if($currentMonth >= 4){
          $nextYear = $currentYear+1;
          $fYear = $currentYear.'-'.$nextYear;
        }
        #quarter Data
        $quarterlyData = $this->quarterlyData();
        $lastQuarterly = $quarterlyData - 1;
        if($lastQuarterly == 0){
          $lastQuarterly = 4;
          $fYear = explode('-', $fYear);
          $fYear0 = $fYear[0]-1;
          $fYear1 = $fYear[1]-1;
          $fYear = $fYear0.'-'.$fYear1;
        }else if($lastQuarterly == -1){
          $lastQuarterly = 3;
        }else if($lastQuarterly == -2){
          $lastQuarterly = 2;
        }
        if($roles[1] == ROLE_SILAI_PC){
          $user = User::load($current_user->id());
          $locationId = $user->field_user_location->target_id;
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_silai_location', $locationId);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->condition('location', $locationId);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
          $currentUserid = $current_user->id();
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
          $query->condition('field_ngo_user_id', $currentUserid);
          $ngoId = $query->execute();
          $ngoId = array_values($ngoId);
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_name_of_ngo', $ngoId[0]);
          $schoolIds = $query->execute();
          
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->condition('ngo_id', $ngoId[0]);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }else{
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }
        #
        $pendingMIS = $totalMISInQuarterly - $submitMIS;
        
      }else if($timeFilter == 2){
        #current FYear
        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth <=3){
          $lastYear = $currentYear-1;
          $fYear = $lastYear.'-'.$currentYear;
        }else if($currentMonth >= 4){
          $nextYear = $currentYear+1;
          $fYear = $currentYear.'-'.$nextYear;
        }
        #quarter Data
        $quarterlyData = $this->quarterlyData();
        $lastQuarterly = $quarterlyData - 2;
        if($lastQuarterly == 0){
          $lastQuarterly = 4;
          $fYear = explode('-', $fYear);
          $fYear0 = $fYear[0]-1;
          $fYear1 = $fYear[1]-1;
          $fYear = $fYear0.'-'.$fYear1;
        }else if($lastQuarterly == -1){
          $lastQuarterly = 3;
          $fYear = explode('-', $fYear);
          $fYear0 = $fYear[0]-1;
          $fYear1 = $fYear[1]-1;
          $fYear = $fYear0.'-'.$fYear1;
        }else if($lastQuarterly == -2){
          $lastQuarterly = 2;
        }
        if($roles[1] == ROLE_SILAI_PC){
          $user = User::load($current_user->id());
          $locationId = $user->field_user_location->target_id;
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_silai_location', $locationId);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->condition('location', $locationId);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
          $currentUserid = $current_user->id();
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
          $query->condition('field_ngo_user_id', $currentUserid);
          $ngoId = $query->execute();
          $ngoId = array_values($ngoId);
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_name_of_ngo', $ngoId[0]);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->condition('ngo_id', $ngoId[0]);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }else{
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }
        $pendingMIS = $totalMISInQuarterly - $submitMIS;
      }else if($timeFilter == 3){
        #current FYear
        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth <=3){
          $lastYear = $currentYear-1;
          $fYear = $lastYear.'-'.$currentYear;
        }else if($currentMonth >= 4){
          $nextYear = $currentYear+1;
          $fYear = $currentYear.'-'.$nextYear;
        }
        #quarter Data
        $quarterlyData = $this->quarterlyData();
        $lastQuarterly = $quarterlyData - 3;
        if($lastQuarterly == 0){
          $lastQuarterly = 4;
        }else if($lastQuarterly == -1){
          $lastQuarterly = 3;
        }else if($lastQuarterly == -2){
          $lastQuarterly = 2;
        }
        if($lastQuarterly != 4){
          $fYear = explode('-', $fYear);
          $fYear0 = $fYear[0]-1;
          $fYear1 = $fYear[1]-1;
          $fYear = $fYear0.'-'.$fYear1;
        }
        if($roles[1] == ROLE_SILAI_PC){
          $user = User::load($current_user->id());
          $locationId = $user->field_user_location->target_id;
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_silai_location', $locationId);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->condition('location', $locationId);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
          $currentUserid = $current_user->id();
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
          $query->condition('field_ngo_user_id', $currentUserid);
          $ngoId = $query->execute();
          $ngoId = array_values($ngoId);
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_name_of_ngo', $ngoId[0]);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->condition('ngo_id', $ngoId[0]);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }else{
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $schoolIds = $query->execute();
          $totalMISInQuarterly = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $fYear);
          $query->condition('monthly_quarterly_type', 1);
          $query->condition('monthly_quarterly_value', $lastQuarterly);
          $query->fields('s');
          $quarterlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($quarterlyMISDatas);
        }
        $pendingMIS = $totalMISInQuarterly - $submitMIS;
      }
    }
    $misData = [
          ['Title', 'Amount'],
          ['Pending- '.$pendingMIS, $pendingMIS],
          ['Submited- '.$submitMIS, $submitMIS]
        ];
    return new JsonResponse($misData);
  }
  # Total Machine (Black & White) Sold
  public function hoadminTotalMachineSold(){
    $filterField = \Drupal::request()->request;  
    $locationID = $filterField->get('location_id');
    $YearFilter = $filterField->get('YearFilter');
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      if(empty($locationID)){
        $user = User::load($current_user->id());
        $locationID = $user->field_user_location->target_id;
      }
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      if(empty($locationID)){
        $masterDataService = \Drupal::service('silai.master_data');
        $currentUserid = $current_user->id();
        $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
        $locationID = $getNgoLocationIds[0];
      }
    }
    /*if(empty($YearFilter)){
      $currentMonth = date('n');
      $currentYear = date('Y');
      if($currentMonth <=3){
        $lastYear = $currentYear-1;
        $fYear = $lastYear.'-'.$currentYear;
      }else if($currentMonth >= 4){
        $nextYear = $currentYear+1;
        $fYear = $currentYear.'-'.$nextYear;
      }
      $YearFilter = $fYear;
    }*/
    $conn = Database::getConnection();
    if($locationID){
      $query = $conn->select('usha_weekly_mis', 's');
      $query->condition('location', $locationID);
      $query->fields('s');
      $datas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $output1 = 0;
      $output2 = 0;
      foreach ($datas as $data) {
        $Month = date("n", $data->week_start_date);
        $Year = date("Y", $data->week_start_date);
        if($Month <=3){
          $lastYear = $Year-1;
          $fYear = $lastYear.'-'.$Year;
        }else if($Month >= 4){
          $nextYear = $Year+1;
          $fYear = $Year.'-'.$nextYear;
        }
        //$Year = date("Y", $data->week_start_date);
        if($YearFilter){
          if($YearFilter == $fYear){
            $output1 = $output1 + $data->black_machines_sold_silai_schools;
            $output2 = $output2 + $data->white_machines_sold_silai_schools;
          }
        }else{
          $output1 = $output1 + $data->black_machines_sold_silai_schools;
          $output2 = $output2 + $data->white_machines_sold_silai_schools;
        }
      }
    }else{
      $query = $conn->select('usha_weekly_mis', 's');
      $query->fields('s');
      $datas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
      $output1 = 0;
      $output2 = 0;
      foreach ($datas as $data) {
        $Month = date("n", $data->week_start_date);
        $Year = date("Y", $data->week_start_date);
        if($Month <=3){
          $lastYear = $Year-1;
          $fYear = $lastYear.'-'.$Year;
        }else if($Month >= 4){
          $nextYear = $Year+1;
          $fYear = $Year.'-'.$nextYear;
        }
        //$Year = date("Y", $data->week_start_date);
        if($YearFilter){
          if($YearFilter == $fYear){
            $output1 = $output1 + $data->black_machines_sold_silai_schools;
            $output2 = $output2 + $data->white_machines_sold_silai_schools;
          }
        }else{
          $output1 = $output1 + $data->black_machines_sold_silai_schools;
          $output2 = $output2 + $data->white_machines_sold_silai_schools;
        }
      }
    }
    $addMachine[]  = $output2 + $output1;
    return new JsonResponse($addMachine);
  }
  # Inventory Pending
  public function hoadminPendingInventory(){
    #Current user role
    $current_user = \Drupal::currentUser();
    $currentRole = $current_user->getRoles();
    $filterField = \Drupal::request()->request;  
    $userRole = $filterField->get('user_role');
    $locationID = $filterField->get('location_id');
    if(empty($userRole)){
      if($currentRole[1] == 'administrator' || $currentRole[1] == ROLE_SILAI_HO_ADMIN || $currentRole[1] == SILAI_HO_USER){
        $userRole = ROLE_SILAI_PC;
      }else if($currentRole[1] == ROLE_SILAI_PC){
        $userRole = ROLE_SILAI_NGO_ADMIN;
      }else if($currentRole[1] == ROLE_SILAI_NGO_ADMIN){
        $userRole = ROLE_SILAI_SCHOOL_ADMIN;
      } 
    }
    if($currentRole[1] == ROLE_SILAI_PC){
      if(empty($locationID)){
        $user = User::load($current_user->id());
        $locationID = $user->field_user_location->target_id;
      }
    }else if($currentRole[1] == ROLE_SILAI_NGO_ADMIN){
      if(empty($locationID)){
        $masterDataService = \Drupal::service('silai.master_data');
        $currentUserid = $current_user->id();
        $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
        $locationID = $getNgoLocationIds[0];
      }
    }
    $conn = Database::getConnection();
    if($userRole == ROLE_SILAI_PC){
      $query = $conn->select(TABLE_CUSTOM_MANAGE_INVENTORY, 's');
      $query->condition(RECEIVER_ROLE, $userRole);
      if($locationID){
        $query->condition('location_id', $locationID);
      }
      $query->condition(STATUS, 1);
      $query->fields('s');
    }else if($userRole == ROLE_SILAI_NGO_ADMIN){
      $query = $conn->select(TABLE_CUSTOM_MANAGE_INVENTORY, 's');
      $query->condition(RECEIVER_ROLE, $userRole);
      if($locationID){
        $query->condition('location_id', $locationID);
      }
      $query->condition(STATUS, 1);
      $query->fields('s');
    }else if($userRole == ROLE_SILAI_SCHOOL_ADMIN){
      $query = $conn->select(TABLE_CUSTOM_MANAGE_INVENTORY, 's');
      $query->condition(RECEIVER_ROLE, $userRole);
      if($locationID){
        $query->condition('location_id', $locationID);
      }
      $query->condition(STATUS, 1);
      $query->fields('s');
    }else{}
    $datas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    $countInventory = 0;
    foreach ($datas as $data) {
      $countInventory++;
    }
    $pendingInventory[] =  $countInventory;
    return new JsonResponse($pendingInventory);
  }
  #
  #Get Learner Ids
  public function learnerIdsForLearnerChart() {
    $masterDataService = \Drupal::service('silai.master_data');
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      $user = User::load($current_user->id());
      $locationId = $user->field_user_location->target_id;
      if(!empty($locationId)) {
        $schoolIds = \Drupal::entityQuery('node')->condition('type', SILAI_SCHOOL)
            ->condition('field_silai_location', $locationId)->condition(STATUS, 1)
            ->condition('field_sil_school_approval_status', 1)
            ->execute();
      
        $schoolIds = array_values($schoolIds);
      }
      if(!empty($locationId) && !empty($schoolIds)) {
        $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
        $query->condition(TYPE, 'silai_learners_manage');
        $query->condition('field_silai_school', $schoolIds, 'IN');
        $learnerIds = $query->execute(); 
      } 
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
     	
      $currentUserid = $current_user->id();
      /*
      // $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
      // $query->condition('field_ngo_user_id', $currentUserid);
      // $ngoId = $query->execute();
      $ngoId = $masterDataService->getLinkedNgoForUser($currentUserid);
      //$ngoId = array_values($ngoId);
      if(!empty($ngoId)) {
        $schoolId = \Drupal::entityQuery('node')
            ->condition('type', SILAI_SCHOOL)
            ->condition('field_name_of_ngo', $ngoId[$currentUserid])
            ->condition('field_sil_school_approval_status', 1)
            ->condition(STATUS, 1)
            ->execute();
        $schoolId = array_values($schoolId);
      }
      if(!empty($ngoId) && !empty($schoolId)) {
        $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
        $query->condition(TYPE, 'silai_learners_manage');
        $query->condition('field_silai_school', $schoolId, 'IN');
        $learnerIds = $query->execute();
      }
     */
     $learnerIds = $masterDataService->getAllLearnerAssWithNgo($currentUserid);
    }else{
      $learnerIds = \Drupal::entityQuery(NODE)->condition(STATUS, 1)->condition(TYPE, 'silai_learners_manage')->execute();
    }
    return $learnerIds;
  }
  
  #
  public function schoolIdsForSchoolChartCount(){
    $masterDataService = \Drupal::service('silai.master_data');
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      $user = User::load($current_user->id());
      $locationId = $user->field_user_location->target_id;
      $contentType = SILAI_SCHOOL;
      $SchoolCount = $masterDataService->getNodeCountForChart($contentType, $locationId); 
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      $currentUserid = $current_user->id();
      // $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
      // $query->condition('field_ngo_user_id', $currentUserid);
      // $ngoId = $query->execute();
      $ngoId = $masterDataService->getLinkedNgoForUser($currentUserid);
      // print_r($ngoId);
      // die();
      $schoolIds = \Drupal::entityQuery('node')
            ->condition('type', SILAI_SCHOOL)
            ->condition('field_name_of_ngo', $ngoId[$currentUserid])
            ->condition('field_sil_school_approval_status', 1)
            ->condition('status', 1)
            ->execute();
      $SchoolCount =  count($schoolIds); 
      
    }else{
      $contentType = SILAI_SCHOOL;
      $SchoolCount = $masterDataService->getNodeCountForChart($contentType, $locationId); 
    }
    return $SchoolCount;
  }
  public function pendingAmountForAgreement(){
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if($roles[1] == ROLE_SILAI_PC){
      $user = User::load($current_user->id());
      $locationId = $user->field_user_location->target_id;
      $agreementDatas = $this->agreementQueryByLocation($locationId);
      foreach ($agreementDatas as $agreementData) {
        $pendingAgreementamount = $pendingAgreementamount + $agreementData->field_silai_agree_due_balance_value;
      }
    }else if($roles[1] == ROLE_SILAI_NGO_ADMIN){
      $masterDataService = \Drupal::service('silai.master_data');
      $currentUserid = $current_user->id();
      $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
      //print_r($getNgoLocationIds);
      foreach ($getNgoLocationIds as $locationId) {
        $agreementDatas = $this->agreementQueryByLocation($locationId);
        foreach ($agreementDatas as $agreementData) {
          $pendingAgreementamount = $pendingAgreementamount + $agreementData->field_silai_agree_due_balance_value;
        }
      }
    }else{
      $agreementIds = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'manage_agreements')
        ->execute();
      $agreementNodes = \Drupal\node\Entity\Node::loadMultiple($agreementIds);
      $pendingAgreementamount = 0;
      //print_r($agreementNodes);
      //die();
      foreach($agreementNodes as $agreementNode) {
        $pendingAgreementamount = $pendingAgreementamount + $agreementNode->field_silai_agree_due_balance->value;
      }
    }
    return $pendingAgreementamount;
  }
  function agreementQueryByLocation($locationId){
    $db = Database::getConnection();
    $query = $db->select('node_field_data', 'n');
    $query->join('node__field_agreement_ngo_name', 'ngo', 'ngo.entity_id = n.nid');
    $query->leftJoin('node__field_ngo_location', 'ngo_location', 'ngo_location.entity_id = ngo.field_agreement_ngo_name_target_id');
    $query->leftJoin('node__field_agreement_amount', 'agree_amount', 'agree_amount.entity_id = n.nid');
    $query->leftJoin('node__field_silai_agree_due_balance', 'due_amount', 'due_amount.entity_id = n.nid');
    $query->leftJoin('node__field_silai_agre_received_amount', 'rec_amount', 'rec_amount.entity_id = n.nid');
    $query->fields('n', ['nid', 'created']);
    $query->fields('ngo_location', ['field_ngo_location_target_id']);
    $query->fields('agree_amount', ['field_agreement_amount_value']);
    $query->fields('due_amount', ['field_silai_agree_due_balance_value']);
    $query->fields('rec_amount', ['field_silai_agre_received_amount_value']);
    $query->condition('ngo_location.bundle', ['ngo']);
    $query->condition('ngo_location.field_ngo_location_target_id', $locationId);
    $agreementDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    return $agreementDatas;
  }
  #
  function Start_End_Date_of_a_week($week, $year){
    $time = strtotime("1 January $year", time());
    $day = date('w', $time);
    $time += ((7*$week)+1-$day)*24*3600;
    $dates[0] = date('d-M-Y', $time);
    $time += 6*24*3600;
    $dates[1] = date('d-M-Y', $time);
    return $dates;
  }
  function weekMISDataArray($weekArray){
    $weekStartDate = strtotime($weekArray[0]);
    $weekEndDate = strtotime($weekArray[1]);
    $conn = Database::getConnection();
    $query = $conn->select('usha_weekly_mis', 's');
    $query->condition('week_start_date', $weekStartDate);
    $query->condition('week_end_date', $weekEndDate);
    $query->fields('s');
    $weeklyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    return $weeklyMISDatas;
  }
  #
  function getMonthData($lastMonth){
    if($lastMonth == 0){
      $lastMonth = 12;
    }else if($lastMonth == -1){
      $lastMonth = 11;
    }else if($lastMonth == -2){
      $lastMonth = 10;
    }else{
      $lastMonth = $lastMonth;
    }
    return  $lastMonth;
  }
  #
  function learnerEnrolledDateFYear($enrolledYear, $enrolledMonth){
    if($enrolledMonth <=3){
      $lastYear = $enrolledYear-1;
      $fYear = $lastYear.'-'.$enrolledYear;
    }else if($enrolledMonth >= 4){
      $nextYear = $enrolledYear+1;
      $fYear = $enrolledYear.'-'.$nextYear;
    }
    return $fYear;
  }
  function getCurrentFYear($getMonth){
    $currentYear = date('Y');
    if($getMonth <=3){
      $lastYear = $currentYear-1;
      $fYear = $lastYear.'-'.$currentYear;
    }else if($getMonth >= 4){
      $nextYear = $currentYear+1;
      $fYear = $currentYear.'-'.$nextYear;
    }
    return $fYear;
  }
  function quarterlyData(){
    if(date('M') == 'Jan' || date('M') == 'Feb'|| date('M') == 'Mar'){
      $quarterlyData = 4;
    }else if(date('M') == 'Apr' || date('M') == 'May'|| date('M') == 'Jun'){
      $quarterlyData = 1;
    }else if(date('M') == 'Jul' || date('M') == 'Aug'|| date('M') == 'Sep'){
      $quarterlyData = 2;
    }else if(date('M') == 'Oct' || date('M') == 'Nov'|| date('M') == 'Dec'){
      $quarterlyData = 3;
    }
    return $quarterlyData;
  }
  function isLeap($year){  
    return (date('L', mktime(0, 0, 0, 1, 1, $year))==1);  
  } 
  function getWeeklyMisPendingAndSubmitData($lastWeek, $locationID){
    $weekArray = $this->Start_End_Date_of_a_week($lastWeek,date('Y'));
    $weeklyMISDatas = $this->weekMISDataArray($weekArray);
    $submitMIS = count($weeklyMISDatas);
    $totalMISInWeek = count($locationID);
    $pendingMIS = $totalMISInWeek - $submitMIS;
    $submitAndPendingMIS['submitMIS'] = $submitMIS;
    $submitAndPendingMIS['pendingMIS'] = $pendingMIS;
    return $submitAndPendingMIS;
  }

  function getMonthlyMisPendingAndSubmitData($lastMonth){
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    //$lastMonth = date('n')-1;
        $getMonth = $this->getMonthData($lastMonth);
        $getCurrentFYear = $this->getCurrentFYear($getMonth);
        if($roles[1] == ROLE_SILAI_PC){
          $user = User::load($current_user->id());
          $locationId = $user->field_user_location->target_id;
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_silai_location', $locationId);
          $schoolIds = $query->execute();
          $totalMISInMonth = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $getCurrentFYear);
          $query->condition('monthly_quarterly_type', 0);
          $query->condition('monthly_quarterly_value', $getMonth);
          $query->condition('location', $locationId);
          $query->fields('s');
          $monthlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($monthlyMISDatas);
        }elseif($roles[1] == ROLE_SILAI_NGO_ADMIN){
          //$user = User::load($current_user->id());
          //$locationId = $user->field_user_location->target_id;
          $currentUserid = $current_user->id();
          $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'ngo');
          $query->condition('field_ngo_user_id', $currentUserid);
          $ngoId = $query->execute();
          $ngoId = array_values($ngoId);
          #
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $query->condition('field_name_of_ngo', $ngoId[0]);
          $schoolIds = $query->execute();
          $totalMISInMonth = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $getCurrentFYear);
          $query->condition('monthly_quarterly_type', 0);
          $query->condition('monthly_quarterly_value', $getMonth);
          $query->condition('ngo_id', $ngoId[0]);
          $query->fields('s');
          $monthlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($monthlyMISDatas);
        }else{
          $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
          $query->condition(TYPE, 'silai_school');
          $query->condition('field_sil_school_approval_status', 1);
          $schoolIds = $query->execute();
          $totalMISInMonth = count($schoolIds);
          #
          $conn = Database::getConnection();
          $query = $conn->select('usha_monthly_mis', 's');
          $query->condition('fiscal_year', $getCurrentFYear);
          $query->condition('monthly_quarterly_type', 0);
          $query->condition('monthly_quarterly_value', $getMonth);
          $query->fields('s');
          $monthlyMISDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
          $submitMIS = count($monthlyMISDatas);
        }
        $pendingMIS = $totalMISInMonth - $submitMIS;
      $submitAndPendingMIS['submitMIS'] = $submitMIS;
      $submitAndPendingMIS['pendingMIS'] = $pendingMIS;
      return $submitAndPendingMIS;
  }
   function getLearnerCount($fyFilter, $locationId = NULL, $field = 'field_silai_date_of_enrollment_value'){
    $db = Database::getConnection();
    $fieldArray = explode('_value', $field);
    $query = $db->select('node_field_data', 'n');
    $query->join('node__'.$fieldArray[0], 'ad_stu', 'ad_stu.entity_id = n.nid');
    if($locationId){
      $query->join('node__field_silai_school', 'stu_school', 'stu_school.entity_id = n.nid AND stu_school.bundle = :school', [':school' => 'silai_learners_manage']);
      $query->join('node__field_silai_location', 'stu_location', 'stu_school.field_silai_school_target_id = stu_location.entity_id AND stu_location.bundle = :location', [':location' => 'silai_school']);
    }
    $query->addExpression("DATE_FORMAT(ad_stu.".$field.", '%b')", 'month');
    $query->addExpression('COUNT(n.nid)', 'count');

    if($locationId){
      //$query->condition('stu_location.bundle', ['manage_sewing_students']);
      $query->condition('stu_location.field_silai_location_target_id', $locationId);
    }
    if(!empty($fyFilter)){
      $yearExplode = explode('-', $fyFilter);
      $query->condition('ad_stu.'.$field, $yearExplode[0].'-04-01','>=');
      $query->condition('ad_stu.'.$field, $yearExplode[1].'-03-31','<=');
    }
    $query->groupBy("MONTH(ad_stu.".$field.")");
    $studentDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    // dump($query->__toString());
    // die();
    return $studentDatas;
  }
  #
}
