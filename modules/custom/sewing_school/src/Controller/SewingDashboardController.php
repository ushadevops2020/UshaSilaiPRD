<?php

namespace Drupal\sewing_school\Controller;

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

/**
 * AdminDashboardController class.
 */
class SewingDashboardController extends ControllerBase {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   *   DashBoard page 
   */
  public function dashboardPageManage() {
    $masterDataService = \Drupal::service('sewing.master_data');
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    /** Dashboard Gallery For HO User SSI and School user  **/
    if(ROLE_SEWING_SSI == $roles[1]) {
      $userData = User::load($current_user->id());
      $loactionId[] = $userData->field_user_location->target_id; 
      $viewGallery = Views::getView('sewing_gallery_view');
      $viewGallery->setDisplay('block_3');
      $viewGallery->setArguments($loactionId);
      $sewingViewGalleryRender = $viewGallery->render();
      /* Notice Board*/
      $viewNotice = Views::getView('sewing_broadcast_notification');
      $viewNotice->setDisplay('block_2');
      $viewNoticeRender = $viewNotice->render();
    }else{
      $viewGallery = Views::getView('sewing_gallery_view');
      $viewGallery->setDisplay('block_1');
      $sewingViewGalleryRender = $viewGallery->render();

      /* Notice Board*/
      $viewNotice = Views::getView('sewing_broadcast_notification');
      $viewNotice->setDisplay('block_1');
      $viewNoticeRender = $viewNotice->render();
    }
	if(ROLE_SEWING_HO_ADMIN == $roles[1]) {
		$query = \Drupal::entityQuery('node');
			$query->condition('type', 'sewing_school');
			$query->condition(STATUS, 1);
			$query->condition('field_sew_school_approval_status', 1);
			$query->condition('field_sewing_date_of_renewal', date('Y-m-d'), '<');
		$renewalSchoolCount = $query->count()->execute();
	}else if(ROLE_SEWING_SSI == $roles[1]) {
		$userData = User::load($current_user->id());
		$loactionId[] = $userData->field_user_location->target_id;
		$query = \Drupal::entityQuery('node');
			$query->condition('type', 'sewing_school');
			$query->condition(STATUS, 1);
			$query->condition('field_location', $loactionId, 'IN');
			$query->condition('field_sew_school_approval_status', 1);
			$query->condition('field_sewing_date_of_renewal', date('Y-m-d'), '<');
		$renewalSchoolCount = $query->count()->execute();
	}
   
    return [
            '#title' => 'Dashboard',
            '#theme' => 'sewing_dashboard_page',
            '#role' => $roles[1],
            '#sewingViewGalleryRender' => $sewingViewGalleryRender,
            '#viewNoticeRender' => $viewNoticeRender,
			'#renewalSchoolCount' => $renewalSchoolCount,
        ];
  }
  # Dashboard for School Admin Page 
  public function dashboardPageManageForSchoolAdmin(){
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    /** Dashboard Gallery For School user  **/
    if(ROLE_SEWING_SCHOOL_ADMIN == $roles[1]) {
      $schoolCode = $this->getCurrentUserSchoolCode($current_user->id());

      $userId = $current_user->id();
      $schoolId = \Drupal::entityQuery('node')
            ->condition('type', 'sewing_school')
            ->condition('field_sewing_user_id', $current_user->id())
            //->condition('field_sil_school_approval_status', 1)
            //->condition('status', 1)
            ->execute();
      $schoolId = array_values($schoolId);
      $locationId[] = Node::load($schoolId[0])->field_location->target_id;
      $viewGallery = Views::getView('sewing_gallery_view');
      $viewGallery->setDisplay('block_2');
      $viewGallery->setArguments($locationId);
      $sewingViewGalleryRender = $viewGallery->render();
      /* Notice Board*/
      $viewNotice = Views::getView('sewing_broadcast_notification');
      $viewNotice->setDisplay('block_3');
      $viewNoticeRender = $viewNotice->render();
    }
    return [
            '#title' => 'Dashboard',
            '#theme' => 'sewing_dashboard_page_for_school_admin',
            '#role' => $roles[1],
            '#sewingViewGalleryRender' => $sewingViewGalleryRender,
            '#viewNoticeRender' => $viewNoticeRender,
            '#schoolCode' => $schoolCode,
        ];
  }
  # Filter List for Location
  public function sewingDashboardLoctionFilter(){
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    # All location for HO Admin & Ho User
    if(ROLE_SEWING_SSI == $roles[1]) {
      $userData = User::load($current_user->id());
      $loactionId[] = $userData->field_user_location->target_id; 
      $locationsNodes = \Drupal\node\Entity\Node::loadMultiple($loactionId);
      foreach($locationsNodes as $locationsNode) {
        $locations[$locationsNode->id()] = $locationsNode->getTitle();
      }
    }else if(ROLE_SEWING_SCHOOL_ADMIN == $roles[1]) {
      $userId = $current_user->id();
      $schoolId = \Drupal::entityQuery('node')
            ->condition('type', 'sewing_school')
            ->condition('field_sewing_user_id', $current_user->id())
            //->condition('field_sil_school_approval_status', 1)
            //->condition('status', 1)
            ->execute();
      $schoolId = array_values($schoolId);
      $locationIds[] = Node::load($schoolId[0])->field_location->target_id;
      $locationsNodes = \Drupal\node\Entity\Node::loadMultiple($loactionId);
      foreach($locationsNodes as $locationsNode) {
        $locations[$locationsNode->id()] = $locationsNode->getTitle();
      }
    }else{
      $locationsNodes = \Drupal\node\Entity\Node::loadMultiple($this->getLocationIds());
      foreach($locationsNodes as $locationsNode) {
        $locations[$locationsNode->id()] = $locationsNode->getTitle();
      }
    }
    $return = ['data' => $locations, STATUS => 1];    
    return new JsonResponse($return);
  }
  #Filter List For Financial Year
  public function sewingDashboardFinancialYearFilter(){
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
    $yearData['All'] = 'All';
    $return = ['data' => $yearData, STATUS => 1]; 
    return new JsonResponse($return);
  }
  #Filter List For Financial Year
  public function sewingDashboardFinancialYearFilterForSchool(){
    $currentYear = date('Y');
    $currentMonth = date('n');
    //$yearData['All'] = 'All';
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
  # School chart Data prepare
  public function sewingDashboardSchoolChartDataset(){
    $filterField = \Drupal::request()->request; 
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $locationId = $filterField->get('location_id');
    $fyFilter = $filterField->get('fy_filter');
    # Default Location Filter
    if(empty($locationId)){
      $getLocationId = $this->getCurrentUserLocation();
      $locationId = $getLocationId[0];
    }
    # Default FY Filter
    // if(empty($fyFilter)){
    //   $currentYear = date("Y");
    //   $currentMonth = date("n");
    //   $fyFilter = $this->generateDateToFYear($currentYear, $currentMonth);
    // }else if($fyFilter == 'All'){
    //   $fyFilter = '';
    // }
    if($fyFilter == 'All'){
      $fyFilter = '';
    }
    $schoolTypeIds = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'school_type_master')
      //->condition('status', 1)
      ->execute();
    $schoolChartData[0] = ['School Type', 'No. of Schools'];
    $i= 1;
    foreach ($schoolTypeIds as $schoolTypeId) {
      $countSchoolArray1 = 0;
      $countSchoolArray2 = 0;
      $schoolTypeData = Node::load($schoolTypeId);
      if($locationId){
        if($fyFilter){
          $schoolIds = $this->getSchoolIds($schoolTypeId, $locationId);
          foreach ($schoolIds as $schoolId) {
            $schoolData = Node::load($schoolId);
            $termName = Term::load($schoolData->field_sewing_financial_year->target_id)->name->value;
            //$termName = $termData->name->value;
            if($termName == $fyFilter){
                $countSchoolArray1++;
            }
          }
        }else{
          $schoolIds = $this->getSchoolIds($schoolTypeId, $locationId);
          $schoolCount = count($schoolIds);
        }
      }else{
        if($fyFilter){
          $schoolIds = $this->getSchoolIds($schoolTypeId, $locationId);
          foreach ($schoolIds as $schoolId) {
            $schoolData = Node::load($schoolId);
            $termName = Term::load($schoolData->field_sewing_financial_year->target_id)->name->value;
            //$termName = $termData->name->value;
            if($termName == $fyFilter){
                $countSchoolArray2++;
            }
          }
        }else{
          $schoolIds = $this->getSchoolIds($schoolTypeId, $locationId);
          $schoolCount = count($schoolIds);
        }
      }
      $schoolCount = count($schoolIds);
      if($locationId){
        if($fyFilter){
          $schoolChartData[$i][] = $schoolTypeData->field_school_type_code->value.' ('.$countSchoolArray1.')';
          $schoolChartData[$i][] = $countSchoolArray1;
        }else{
          $schoolChartData[$i][] = $schoolTypeData->field_school_type_code->value.' ('.$schoolCount.')';
          $schoolChartData[$i][] = $schoolCount;
        }
      }else{
        if($fyFilter){
          $schoolChartData[$i][] = $schoolTypeData->field_school_type_code->value.' ('.$countSchoolArray2.')';
          $schoolChartData[$i][] = $countSchoolArray2;
        }else{
          $schoolChartData[$i][] = $schoolTypeData->field_school_type_code->value.' ('.$schoolCount.')';
          $schoolChartData[$i][] = $schoolCount;
        }
      }
     // $schoolChartData[$i][] = $schoolCount;
      $i++;
    }
    $totalSchool = 0;
    foreach ($schoolChartData as $schoolData) {
      if(is_numeric($schoolData[1])){
        $totalSchool = $totalSchool + $schoolData[1];
      }
    }
    $result['chart'] = $schoolChartData;
    $result['count'] = $totalSchool;
    return new JsonResponse($result);
  }
  # Student chart Data prepare
  public Function sewingDashboardStudentChartDataset(){
    $filterField = \Drupal::request()->request; 
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $locationId = $filterField->get('location_id');
    $fyFilter = $filterField->get('fy_filter');
    # Default Location Filter
    if(empty($locationId)){
      $getLocationId = $this->getCurrentUserLocation();
      $locationId = $getLocationId[0];
    }
    # Default FY Filter
    if(empty($fyFilter)){
      $currentYear = date("Y");
      $currentMonth = date("n");
      $fyFilter = $this->generateDateToFYear($currentYear, $currentMonth);
    }else if($fyFilter == 'All'){
      $fyFilter = 'All';
    }
    $studentAdmissionArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    $studentCompletionArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    # Get Student Ids
    
    $getEnrolledStudentCount = $this->getStudentCount($fyFilter, $locationId, 'field_student_admission_date_value');
    foreach ($getEnrolledStudentCount as $key => $value) {
      $studentAdmissionArray[$value->month] = (int)$value->count;
    }
    $getCompletedStudentCount = $this->getStudentCount($fyFilter, $locationId, 'field_sew_course_completion_date_value');
     foreach ($getCompletedStudentCount as $key => $value) {
      $studentCompletionArray[$value->month] = (int)$value->count;
    }

    $totalEnrolled = 0;
    $courseCompleted = 0;
    foreach ($studentAdmissionArray as $key => $value) {
      $arrDatas[] = [$key, $value, $studentCompletionArray[$key]];
      $totalEnrolled =  $totalEnrolled + $value;
      $courseCompleted =  $courseCompleted + $studentCompletionArray[$key];
    }
    $a = 1;
    $studentChartData[0] = ['Month', 'Total Enrolled- '.$totalEnrolled, 'Course Completed- '.$courseCompleted];
    foreach ($arrDatas as $arrData) {
      $studentChartData[$a][] = $arrData[0];
      $studentChartData[$a][] =  $arrData[1];
      $studentChartData[$a][] =  $arrData[2];
      $a++;
    }
    $result['chart'] = $studentChartData;
    $result['count'] = number_format($totalEnrolled);
    return new JsonResponse($result);
  }
   # Student chart Data prepare (School Admin)
  public Function sewingDashboardStudentChartDatasetSA(){
    $filterField = \Drupal::request()->request; 
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    //$locationId = $filterField->get('location_id');
    $fyFilter = $filterField->get('fy_filter');
    # Default FY Filter
    if(empty($fyFilter)){
      $currentYear = date("Y");
      $currentMonth = date("n");
      $fyFilter = $this->generateDateToFYear($currentYear, $currentMonth);
    }else if($fyFilter == 'All'){
      $fyFilter = '';
    }
    $studentAdmissionArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    $studentCompletionArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    $schoolId = $this->getCurrentUserSchoolID($current_user->id());
    // $schoolTypeIds = \Drupal::entityQuery(NODE)
    //   ->condition(TYPE, 'school_type_master')
    //   ->condition('status', 1)
    //   ->execute();
    // foreach ($schoolTypeIds as $schoolTypeId) {
    //   $schoolTypeData = Node::load($schoolTypeId);
    //   $schoolTypeTitle = $schoolTypeData->getTitle(); 
    //   $onRollStudentArray[$schoolTypeTitle] = 0;
    //   $notOnRollStudentArray[$schoolTypeTitle] = 0;
    // }
    # Get Student Ids
    $studentQuery = \Drupal::entityQuery(NODE)->condition(TYPE, 'manage_sewing_students')->condition('field_sewing_school_code_list', $schoolId);
    //$studentQuery->condition('status', 1);
    $studentIds = $studentQuery->execute();
    $studentDatas = Node::loadMultiple($studentIds);
     foreach ($studentDatas as $studentData) {
      //$studentStatus = $studentData->status->value;
      $studentLoaction = $studentData->field_location->target_id;
      $studentAdmissionDate = $studentData->field_student_admission_date->value;

      $studentAdmissionYear = date("Y", strtotime($studentAdmissionDate));
      $studentAdmissionMonthNo = date("n", strtotime($studentAdmissionDate));
      $studentAdmissionFY = $this->generateDateToFYear($studentAdmissionYear, $studentAdmissionMonthNo);
      $studentAdmissionMonth = date("M", strtotime($studentAdmissionDate));
      #Get EnRoll Student Data
      if($locationId){
        if($locationId == $studentLoaction){
          if($fyFilter){
            if($fyFilter == $studentAdmissionFY){
              if($studentAdmissionMonth){
                $studentAdmissionArray[$studentAdmissionMonth] = $studentAdmissionArray[$studentAdmissionMonth] + 1;
              }
            }
          }else{
            if($studentAdmissionMonth){
              $studentAdmissionArray[$studentAdmissionMonth] = $studentAdmissionArray[$studentAdmissionMonth] + 1;
            }
          }
        }
      }else{
        if($fyFilter){
          if($fyFilter == $studentAdmissionFY){
            if($studentAdmissionMonth){
              $studentAdmissionArray[$studentAdmissionMonth] = $studentAdmissionArray[$studentAdmissionMonth] + 1;
            }
          }
        }else{
          if($studentAdmissionMonth){
            $studentAdmissionArray[$studentAdmissionMonth] = $studentAdmissionArray[$studentAdmissionMonth] + 1;
          }
        }
      }
      #course Completion data array create
      $studentCourseCompletionDate = $studentData->field_sew_course_completion_date->value;

      $studentCourseCompletionYear = date("Y", strtotime($studentCourseCompletionDate));
      $studentCourseCompletionMonthNo = date("n", strtotime($studentCourseCompletionDate));
      $studentCourseCompletionFY = $this->generateDateToFYear($studentCourseCompletionYear, $studentCourseCompletionMonthNo);
      $studentCourseCompletionMonth = date("M", strtotime($studentCourseCompletionDate));
      #Get Not On Roll Student Data
      if(!empty($studentCourseCompletionDate)){
        if($locationId){
          if($locationId == $studentLoaction){
            if($fyFilter){
              if($fyFilter == $studentCourseCompletionFY){
                if($studentCourseCompletionMonth){
                  $studentCompletionArray[$studentCourseCompletionMonth] = $studentCompletionArray[$studentCourseCompletionMonth] + 1;
                }
              }
            }else{
              if($studentCourseCompletionMonth){
                $studentCompletionArray[$studentCourseCompletionMonth] = $studentCompletionArray[$studentCourseCompletionMonth] + 1;
              }
            }
          }
        }else{
          if($fyFilter){
            if($fyFilter == $studentCourseCompletionFY){
              if($studentCourseCompletionMonth){
                $studentCompletionArray[$studentCourseCompletionMonth] = $studentCompletionArray[$studentCourseCompletionMonth] + 1;
              }
            }
          }else{
            if($studentCourseCompletionMonth){
              $studentCompletionArray[$studentCourseCompletionMonth] = $studentCompletionArray[$studentCourseCompletionMonth] + 1;
            }
          }
        }
      }
    }
    // print_r($studentAdmissionArray);
    // print_r($studentCompletionArray);
    // die();
    $totalEnrolled = 0;
    $courseCompleted = 0;
    foreach ($studentAdmissionArray as $key => $value) {
      $arrDatas[] = [$key, $value, $studentCompletionArray[$key]];
      $totalEnrolled =  $totalEnrolled + $value;
      $courseCompleted =  $courseCompleted + $studentCompletionArray[$key];
    }
    $a = 1;
    $studentChartData[0] = ['Month', 'Total Enrolled- '.$totalEnrolled, 'Course Completed- '.$courseCompleted];
    foreach ($arrDatas as $arrData) {
      $studentChartData[$a][] = $arrData[0];
      $studentChartData[$a][] =  $arrData[1];
      $studentChartData[$a][] =  $arrData[2];
      $a++;
    }
    $result['chart'] = $studentChartData;
    $result['count'] = $totalEnrolled;
    return new JsonResponse($result);
  }
  # Revenue chart Data prepare
  public Function sewingDashboardRevenueChartDataset(){
    $filterField = \Drupal::request()->request; 
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $locationId = $filterField->get('location_id');
    $fyFilter = $filterField->get('fy_filter');
    # Default Location Filter
    if(empty($locationId)){
      $getLocationId = $this->getCurrentUserLocation();
      $locationId = $getLocationId[0];
    }
    # Default FY Filter
    if(empty($fyFilter)){
      $currentYear = date("Y");
      $currentMonth = date("n");
      $fyFilter = $this->generateDateToFYear($currentYear, $currentMonth);
    }else if($fyFilter == 'All'){
      $fyFilter = 'All';
    }
	//$fyFilter = 'All';
    # Revenue Head Data get
    $revenueTypeIds = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'revenue')
      //->condition('status', 1)
      ->execute();
    $revenueTypeNodes = Node::loadMultiple($revenueTypeIds);
    foreach ($revenueTypeNodes as $revenueTypeNode) {
      $revenueTypeTitle = $revenueTypeNode->getTitle();
      $revenueTypeId = $revenueTypeNode->id();
      $revenueArray[$revenueTypeId] = 0;
      $revenueTitleArray[$revenueTypeId] = $revenueTypeNode->field_revenue_code->value;
    }
    foreach ($revenueTypeNodes as $revenueTypeNode) {
      $revenueTypeTitle = $revenueTypeNode->getTitle();
      $revenueTypeId = $revenueTypeNode->id();
      if(REVENUE_HEAD_STUDENT_FEE_NID != $revenueTypeId){
		    $revenueDataArray = $this->getRevenueAmmountArray($revenueTypeId, $locationId, $fyFilter);
			foreach ($revenueDataArray as $revenueData) {
				$revenueHead = (int)$revenueData->revenue_head_type;
			    $revenueArray[$revenueHead] = (int)$revenueData->fee_value;
			}
      }else if(REVENUE_HEAD_STUDENT_FEE_NID == $revenueTypeId){
		$revenueStudentDataArray = $this->getStudentRevenueAmmountArray($revenueTypeId, $locationId, $fyFilter);
		
		foreach ($revenueStudentDataArray as $revenueStudentData) {
			    $revenueArray[REVENUE_HEAD_STUDENT_FEE_NID] = (int)$revenueStudentData->fee_value;
			}
      }
	  
    }
    foreach ($revenueArray as $key => $value) {
		if(!empty($key)){
			$arrDatas[] = [$key, $value];
		}
    }
    $a = 1;
    $revenueChartData[0] = ['School Type', 'Amount'];
    foreach ($arrDatas as $arrData) {
		if($arrData[1] != 0){
			$revenueChartData[$a][] = $revenueTitleArray[$arrData[0]];
			$revenueChartData[$a][] =  $arrData[1];
			$a++;
		}
    }
	//print_r($revenueChartData);
	//die;
    $totalRevenue = 0;
    foreach ($revenueChartData as $revenueData) {
      if(is_numeric($revenueData[1])){
        $totalRevenue = $totalRevenue + $revenueData[1];
      }
    }
    $result['chart'] = $revenueChartData;
    $result['count'] = number_format($totalRevenue);
    return new JsonResponse($result);
  }
  # Revenue chart Data prepare
  public Function sewingDashboardMachineChartDataset(){
    $filterField = \Drupal::request()->request; 
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $locationId = $filterField->get('location_id');
    $fyFilter = $filterField->get('fy_filter');
    # Default Location Filter
    if(empty($locationId)){
      $getLocationId = $this->getCurrentUserLocation();
      $locationId = $getLocationId[0];
    }
    # Default FY Filter
    if(empty($fyFilter)){
      $currentYear = date("Y");
      $currentMonth = date("n");
      $fyFilter = $this->generateDateToFYear($currentYear, $currentMonth);
    }else if($fyFilter == 'All'){
      $fyFilter = '';
    }
    $conn = Database::getConnection();
    $query = $conn->select('usha_sewing_weekly_mis', 'f')->condition('is_deleted', 0)
        ->fields('f');
    $weeklyMISRecords = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    $blackMachineArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    $whiteMachineArray = ['Apr' => 0, 'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 'Feb' => 0, 'Mar' => 0];
    foreach ($weeklyMISRecords as $weeklyMISRecord) {
      //  print_r($weeklyMISRecord->week_start_date);
      $weekStartDateYear = date("Y", $weeklyMISRecord->week_start_date);
      $weekStartDateMonth = date("n", $weeklyMISRecord->week_start_date);
      $weeklyDateFY = $this->generateDateToFYear($weekStartDateYear, $weekStartDateMonth);

      $weeklyDateMonthcheck = date("M", $weeklyMISRecord->week_start_date);
      $misLocationId = $weeklyMISRecord->location;
      #black Machine Count
      if($locationId){
        if($locationId == $misLocationId){
          if($fyFilter){
            if($fyFilter == $weeklyDateFY){
              if($weeklyDateMonthcheck){
                $blackMachineArray[$weeklyDateMonthcheck] = $blackMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->black_machines_sold_through_sewing_schools;
              }
            }
          }else{
            if($weeklyDateMonthcheck){
              $blackMachineArray[$weeklyDateMonthcheck] = $blackMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->black_machines_sold_through_sewing_schools;
            }
          }
        }
      }else{
        if($fyFilter){
          if($fyFilter == $weeklyDateFY){
            if($weeklyDateMonthcheck){
              $blackMachineArray[$weeklyDateMonthcheck] = $blackMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->black_machines_sold_through_sewing_schools;
            }
          }
        }else{
          if($weeklyDateMonthcheck){
            $blackMachineArray[$weeklyDateMonthcheck] = $blackMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->black_machines_sold_through_sewing_schools;
          }
        }
      }
      #White Machine Count
      if($locationId){
        if($locationId == $misLocationId){
          if($fyFilter){
            if($fyFilter == $weeklyDateFY){
              if($weeklyDateMonthcheck){
                $whiteMachineArray[$weeklyDateMonthcheck] = $whiteMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->white_machines_sold_sewing_schools;
              }
            }
          }else{
            if($weeklyDateMonthcheck){
              $whiteMachineArray[$weeklyDateMonthcheck] = $whiteMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->white_machines_sold_sewing_schools;
            }
          }
        }
      }else{
        if($fyFilter){
          if($fyFilter == $weeklyDateFY){
            if($weeklyDateMonthcheck){
              $whiteMachineArray[$weeklyDateMonthcheck] = $whiteMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->white_machines_sold_sewing_schools;
            }
          }
        }else{
          if($weeklyDateMonthcheck){
            $whiteMachineArray[$weeklyDateMonthcheck] = $whiteMachineArray[$weeklyDateMonthcheck] + $weeklyMISRecord->white_machines_sold_sewing_schools;
          }
        }
      }
      #
    }
    # Preparing Chart Array
    $totalEnrolled = 0;
    $totalBlackMachine = 0;
    $courseCompleted = 0;
    $totalWhiteMachine = 0;
    foreach ($blackMachineArray as $key => $value) {
      $arrDatas[] = [$key, $value, $whiteMachineArray[$key]];
      $totalBlackMachine =  $totalBlackMachine + $value;
      $totalWhiteMachine =  $totalWhiteMachine + $whiteMachineArray[$key];
    }
    $a = 1;
    $machineChartData[0] = ['Month', 'Straight Stitch- '.$totalBlackMachine, 'Usha Janome- '.$totalWhiteMachine];
    foreach ($arrDatas as $arrData) {
      $machineChartData[$a][] = $arrData[0];
      $machineChartData[$a][] =  $arrData[1];
      $machineChartData[$a][] =  $arrData[2];
      $a++;
    }
    $result['chart'] = $machineChartData;
    $totalMachine = $totalBlackMachine + $totalWhiteMachine;
    $result['count'] = number_format($totalMachine);
    return new JsonResponse($result);
  }
  #Get School Ids for School Chart
  function getSchoolIds($schoolTypeId, $locationId){
    $schoolQuery = \Drupal::entityQuery(NODE)->condition(TYPE, 'sewing_school');
    if($schoolTypeId){
      $schoolQuery->condition('field_sewing_school_type', $schoolTypeId);
    }
    $schoolQuery->condition('field_sew_school_approval_status', 1);
    if($locationId){
      $schoolQuery->condition('field_location', $locationId);
    }
    $schoolQuery->condition('status', 1);
    $schoolIds = $schoolQuery->execute();
    return $schoolIds;
  }
  #Generate FY for any date 
  function generateDateToFYear($Year, $Month){
    if($Month <=3){
      $lastYear = $Year-1;
      $fYear = $lastYear.'-'.$Year;
    }else if($Month >= 4){
      $nextYear = $Year+1;
      $fYear = $Year.'-'.$nextYear;
    }
    return $fYear;
  }
  #get Location Ids
  function getLocationIds(){
    $locationIds = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'manage_locations')
      ->condition('status', 1)
      ->execute();
    return $locationIds;
  }
  #get Current user loaction
  function getCurrentUserLocation(){
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    if(ROLE_SEWING_SSI == $roles[1]) {
      $userData = User::load($current_user->id());
      $locationId[] = $userData->field_user_location->target_id; 
    }else if(ROLE_SEWING_SCHOOL_ADMIN == $roles[1]) {
      $userId = $current_user->id();
      $schoolId = \Drupal::entityQuery('node')
            ->condition('type', 'sewing_school')
            ->condition('field_sewing_user_id', $current_user->id())
            //->condition('field_sil_school_approval_status', 1)
            //->condition('status', 1)
            ->execute();
      $schoolId = array_values($schoolId);
      $locationId[] = Node::load($schoolId[0])->field_location->target_id;
    }else{
      $locationId = '';
    }
    return $locationId;
  }
  function getCurrentUserSchoolCode($userId){
    //$userId = $current_user->id();
    $schoolId = \Drupal::entityQuery('node')
          ->condition('type', 'sewing_school')
          ->condition('field_sewing_user_id', $userId)
          //->condition('field_sil_school_approval_status', 1)
          //->condition('status', 1)
          ->execute();
    $schoolId = array_values($schoolId);
    $schoolCode = Node::load($schoolId[0])->field_sewing_school_code->value;
    return $schoolCode;
  }
  function getCurrentUserSchoolID($userId){
    //$userId = $current_user->id();
    $schoolId = \Drupal::entityQuery('node')
          ->condition('type', 'sewing_school')
          ->condition('field_sewing_user_id', $userId)
          //->condition('field_sil_school_approval_status', 1)
          //->condition('status', 1)
          ->execute();
    $schoolId = array_values($schoolId);
    $schoolId = $schoolId[0];
    return $schoolId;
  }
  function getStudentCount($fyFilter, $locationId = NULL, $field = 'field_student_admission_date_value'){
    $db = Database::getConnection();
    $fieldArray = explode('_value', $field);
    $query = $db->select('node_field_data', 'n');
    $query->join('node__'.$fieldArray[0], 'ad_stu', 'ad_stu.entity_id = n.nid');
    if(!empty($locationId)){
      $query->join('node__field_location', 'stu_location', 'stu_location.entity_id = n.nid');
    }
    $query->addExpression("DATE_FORMAT(ad_stu.".$field.", '%b')", 'month');
    $query->addExpression('COUNT(n.nid)', 'count');

    if(!empty($locationId)){
      $query->condition('stu_location.bundle', ['manage_sewing_students']);
      $query->condition('stu_location.field_location_target_id', $locationId);
    }
    if($fyFilter != 'All'){
      $yearExplode = explode('-', $fyFilter);
      $query->condition('ad_stu.'.$field, $yearExplode[0].'-04-01','>=');
      $query->condition('ad_stu.'.$field, $yearExplode[1].'-03-31','<=');
    }
    $query->groupBy("MONTH(ad_stu.".$field.")");
    $studentDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    //dump($query->__toString());
    //die();
    return $studentDatas;
  }
  function getRevenueAmmountArray($revenueTypeId, $locationId = NULL, $fyFilter){
	$connection = \Drupal::database();
	$query = $connection->select('usha_generate_fee_receipt', 'ufr');
	$query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'ufr.school_id = sst.entity_id');
	$query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [':sewingSchool' => 'sewing_school']);
	$query->fields('ufr', ['revenue_head_type']);
	$query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
	//$query->addExpression("DATE_FORMAT(ufr.created_date, '%b')", 'month');
	if($fyFilter != 'All'){
		$yearExplode = explode('-', $fyFilter);
		$year1 = strtotime($yearExplode[0].'-04-01');
		$year2 = strtotime($yearExplode[1].'-03-31');
		$query->condition('ufr.created_date', $year1,'>=');
		$query->condition('ufr.created_date', $year2,'<=');
	}
	if(!empty($locationId)){
		$query->condition('nfl.field_location_target_id', $locationId);
	}
	$query->condition('ufr.revenue_head_type', $revenueTypeId);
	$results = $query->execute()->fetchAll();
	return $results;
  }
  function getStudentRevenueAmmountArray($revenueTypeId, $locationId = NULL, $fyFilter){
	$connection = \Drupal::database();
	$query = $connection->select('usha_generate_fee_receipt', 'ufr');
	$query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'ufr.school_id = sst.entity_id');
	$query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [':sewingSchool' => 'sewing_school']);
	$query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
	if($fyFilter != 'All'){
		$yearExplode = explode('-', $fyFilter);
		$year1 = strtotime($yearExplode[0].'-04-01');
		$year2 = strtotime($yearExplode[1].'-03-31');
		$query->condition('ufr.created_date', $year1,'>=');
		$query->condition('ufr.created_date', $year2,'<='); 
	}
	if(!empty($locationId)){
		$query->condition('nfl.field_location_target_id', $locationId);
	}
	$query->condition('ufr.want_to_add_student_fee', 1);
	$results = $query->execute()->fetchAll(); 
	return $results;
  }
  #
}