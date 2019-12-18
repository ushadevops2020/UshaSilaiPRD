<?php

namespace Drupal\sewing\Controller;

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
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Entity\Query;

/**
 * Class SewingController.
 */
class SewingController extends ControllerBase {
  /**
   * getSewingStates
   * @return string
   */
  public function getSewingStates() {
    $masterDataService = \Drupal::service('location_master.master_data');
    $stateList = $masterDataService->getStatesByLocationId();
    $return = ['data' => $stateList, 'status' => 1];
    return new JsonResponse($return);
  }

  /**
   * getSewing Town By State
   * @return string
   */
  public function getSewingTownByState($stateId) {
    $masterDataService = \Drupal::service('sewing.master_data');
    $townList = $masterDataService->getTownByStateId($stateId);
    $return = ['data' => $townList, 'status' => 1];
    return new JsonResponse($return);
  }  
  /**
   * getSewing School By Town
   * @return string
   */
  public function getSewingSchoolCode() {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); 
    $locationId =  $user->field_user_location->target_id;
    $stateId = $_REQUEST['state'];
    $townId = $_REQUEST['town'];
    $masterDataService = \Drupal::service('sewing.master_data');
    $schoolList = $masterDataService->getSchoolBylocationId($locationId, $stateId, $townId);
    $return = ['data' => $schoolList, 'status' => 1];
    return new JsonResponse($return);
  }

  /**
   * getSewing School Details By School Code
   * @return string
   */
  public function getSchoolDetailsBySchoolCode() {
    $revenueType = $_REQUEST['select'];
    $schoolCode = $_REQUEST['schoolCode'];
    $schoolData = Node::load($schoolCode);
    $masterDataService = \Drupal::service('sewing.master_data');
    $schoolTypeId = $schoolData->field_sewing_school_type->target_id;
    $schoolGradeId = $schoolData->field_sewing_grade->target_id;
    $schooltypeData = Node::load($schoolTypeId);
    $schooltype = $schooltypeData->getTitle();
    $gradeData = Node::load($schoolGradeId);
    $grade = $gradeData->getTitle();
    $sapCode = $schoolData->field_sewing_sap_code->value;
    $schoolAdminId = $schoolData->field_sewing_user_id->target_id;
    $schoolAdminData = User::load($schoolAdminId);
    $schoolAdmin = $schoolAdminData->field_first_name->value;
    
    $affilicationDate = $schoolData->field_sewing_affiliation_date->value;
    $affilicationAmount = $schoolData->field_affiliation_received_fees->value;
    $renewalDate = $schoolData->field_sewing_date_of_renewal->value;
    $renewalAmount = $schoolData->field_renewal_received_fees->value;
    
    if(empty($affilicationAmount)) {
      $affilicationCon = 1;
    } elseif(!empty($affilicationAmount)) {
      $affilicationCon = 2;
    } else {
      $affilicationCon = 0;
    }

    if($affilicationCon == 0 && strtotime($renewalDate) <= time()) {
      $renewalCon = 1;
    } elseif($affilicationCon == 1 && strtotime($renewalDate) <= time()) {
      $renewalCon = 2;
    } else {
      $renewalCon = 0;
    }
      
    $query =\Drupal::entityQuery('node')
        ->condition('type', 'manage_sewing_students')
        ->condition(STATUS, 1)
        ->condition('field_sewing_school_code_list', $schoolCode);
    $ids = $query->execute();
    $noOfStudents = count($ids);
    $noOfCourses = $schoolData->field_no_of_courses->value;

    $gradeData = $masterDataService->getGradeMasterData($schoolCode);
    $revenueTax= $masterDataService->getRevenueHead($revenueType); 
    $revenueStudentTax= $masterDataService->getRevenueHead(REVENUE_HEAD_STUDENT_FEE_NID); 
    
    $data[] = $schooltype;
    $data[] = $grade;
    $data[] = $sapCode;
    $data[] = $schoolAdmin;
    $data[] = $noOfStudents;
    $data[] = $noOfCourses;
    if($revenueType == REVENUE_HEAD_AFFILIATION_FEE_NID) {
      $data[] = $gradeData['field_affiliation_fees']; 
    } elseif($revenueType == REVENUE_HEAD_RENEWAL_FEE_NID) {
      $data[] = $gradeData['field_renewal_fees'];
    } else {
      $data[] = 0;
    }
    $data[] = $revenueTax[$revenueType];
    $data[] = $revenueStudentTax[REVENUE_HEAD_STUDENT_FEE_NID];
    $data[] = $gradeData['field_payable_to_uil'];
    $data['affilicationCon'] = $affilicationCon;
    $data['affilicationDate']= date('m/d/Y',strtotime($affilicationDate));;
    $data['renewalCon'] = $renewalCon;
    $data['renewalDate'] = date('m/d/Y',strtotime($renewalDate));
    $data['schoolTypeId'] = $schoolTypeId;
    $return = ['data' => $data, 'status' => 1];
    return new JsonResponse($return);
  }

  /**
   * getSewing Student By School Code
   * @return string
   */
  public function getSewingStudentBySchoolCode() {
    $schoolCode = $_REQUEST['schoolCode'];
    $feeId = $_REQUEST['feeId'];
    $masterDataService = \Drupal::service('sewing.master_data');
    $studentList = $masterDataService->getStudentBySchoolCode($schoolCode, $feeId);
    $return = ['data' => $studentList, 'status' => 1];
    return new JsonResponse($return);
  }


  /**
   * getSewing school detail By School id
   * @return string
   */
  public function getSchoolDetailById($id) {
    $schoolId = $id;
    $masterDataService = \Drupal::service('sewing.master_data');
    $schoolData = $masterDataService->getSchoolDetailById($schoolId);
    $schoolDetail = [
      'id' => $schoolData->id(),
      'schoolName' => $schoolData->title->value,
      'schoolCode' => $schoolData->field_sewing_school_code->value,
    ];
    
    $return = ['data' => $schoolDetail, 'status' => 1];
    return new JsonResponse($return);
  }

  /**
   * getSewing course detail By course id
   * @return string
   */
  public function getCourseDetailById($id, $Sid) { 
    $courseId = $id;
	$schoolId = $Sid;
    $masterDataService = \Drupal::service('sewing.master_data');
    $courseData = $masterDataService->getCourseDetailById($courseId);
    $courseDurationNode = Node::load($courseData->field_course_duration->target_id);
    $courseDuration = ($courseDurationNode->field_duration->value == 1) ? $courseDurationNode->field_duration->value.' Month' : $courseDurationNode->field_duration->value.' Months';
	
	$gradeData = $masterDataService->getGradeMasterData($schoolId);
	$paymentToUILPercent = $gradeData['field_payable_to_uil'];
	$courseMasterDataFee = Node::load($courseId)->field_course_fee->value;
	$paymentToUILFee = ($paymentToUILPercent/100)*$courseMasterDataFee;
	
    $courseDetail = [
      'id' => $courseData->id(),
      'courseName' => $courseData->title->value,
      'courseCode' => $courseData->field_course_code->value,
      'courseDuration' => $courseDuration,
      'feeDue' => $courseData->field_course_fee->value,
	  'paymentToUILFee' => $paymentToUILFee,
    ];
    
    $return = ['data' => $courseDetail, 'status' => 1];
    return new JsonResponse($return);
  }
  public function getSchoolListByTownId($id) {
    //$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); 
    //$locationId =  $user->field_user_location->target_id;
    //$stateId = $_REQUEST['state'];
    //$townId = $_REQUEST['town'];
    //$masterDataService = \Drupal::service('sewing.master_data');
   // $schoolList = $masterDataService->getSchoolBylocationId($locationId, $stateId, $townId);
      $query = \Drupal::entityQuery(NODE)->condition(STATUS, 1);
      $query->condition(TYPE, 'sewing_school');
      $query->condition('field_sew_school_approval_status', 1);
      $query->condition('field_town_city', $id);
      $schoolIds = $query->execute();
      foreach ($schoolIds as $schoolId) {
         $schoolData = Node::load($schoolId);
         $schoolCode = $schoolData->field_sewing_school_code->value;
         $schoolOption[$schoolId] =  $schoolCode;
      }
    $return = ['data' => $schoolOption, 'status' => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation of getItemsByItemGroup
   * @param  [type] $town_id int
   * @return [type]          Mix
   */
  public function getItemsByItemGroup($itemGroupId) {
    $masterDataService = \Drupal::service('sewing.master_data');
    $itemList = $masterDataService->getItemsByItemGroup($itemGroupId);
    $return = ['data' => $itemList, STATUS => 1];
    return new JsonResponse($return);
  }


  /**
   * Implementation of getSchoolsByLocation
   * @param  [type] $town_id int
   * @return [type]          Mix
   */
  public function getSchoolsByLocation($locationId) {
    $masterDataService = \Drupal::service('sewing.master_data');
    //$schoolList = $masterDataService->getSchoolBylocationId($locationId);
    $schoolList = $masterDataService->getSchoolListBylocationId($locationId);
    $return = ['data' => $schoolList, STATUS => 1];
    return new JsonResponse($return);
  }
  /**
   * Implementation of bulkTrainingTerminate
   * @return [type]          Mix
   */
  public function bulkTrainingTerminate() {
    $filterField = \Drupal::request()->request; 
    $trainingNids = $filterField->get('trainingNids');
    foreach ($trainingNids as $trainingNid) {
      $node = Node::load($trainingNid); 
      $traineeIds =\Drupal::entityQuery('node')
        ->condition('type', 'trainee')
        ->condition(STATUS, 1)
        ->condition('field_trainer_id', $trainingNid)
        ->execute();
        foreach ($traineeIds as $traineeId) {
          $traineeNode = Node::load($traineeId); 
          $traineeNode->set('field_sewing_status', 3);
          $traineeNode->save();
        }
      $node->set('field_sewing_status', 3);
      $node->save();
    }
    $return = ['data' => 1, STATUS => 1];
    drupal_set_message(t('Workshop/Activity has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }
  public function bulkAttendeeTerminate() {
    $filterField = \Drupal::request()->request; 
    $attendeeNids = $filterField->get('attendeeNids');
    foreach ($attendeeNids as $attendeeNid) {
      $attendeeNode = Node::load($attendeeNid);
      $trainingNid = $attendeeNode->field_trainer_id->value;
      $trainingNode = Node::load($trainingNid);
      if($trainingNode->field_training_type->value == 'Workshop'){
        $attendeeNode->set('field_sewing_status', 3);
        $attendeeNode->save();
        $workshopFee = $trainingNode->field_workshop_fees->value;
        $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'trainee');
        $query->condition('field_trainer_id', $trainingNid);
        $query->condition('field_sewing_status', 1);
        $query->condition('status', 1);
        $traineeIds = $query->execute();
        $traineeCount = count($traineeIds);
        $expectedRevenue = $traineeCount * $workshopFee;
        $revenueGenerated = 0;
        foreach ($traineeIds as $traineeId) {
            $traineeData = Node::load($traineeId);
            $revenueGenerated = $revenueGenerated + $traineeData->field_paid_fee->value;
        }
        $trainingNode->set('field_no_of_attendees', $traineeCount);
        $trainingNode->set('field_revenue_generated', $revenueGenerated);
        $trainingNode->set('field_expected_revenue', $expectedRevenue);
        $trainingNode->set('field_training_type', 'Workshop');
        $trainingNode->save();
      }else if($trainingNode->field_training_type->value == 'Activity'){
        $attendeeNode->set('field_sewing_status', 3);
        $attendeeNode->save();
        $query = \Drupal::entityQuery(NODE)->condition(TYPE, 'trainee');
        $query->condition('field_trainer_id', $trainingNid);
        $query->condition('field_sewing_status', 1);
        $query->condition('status', 1);
        $traineeIds = $query->execute();
        $traineeCount = count($traineeIds);
        $trainingNode->set('field_no_of_attendees', $traineeCount);
        $trainingNode->set('field_training_type', 'Activity');
        $trainingNode->save();
      }
    }
    $return = ['data' => 1, STATUS => 1];
    drupal_set_message(t('Attendee has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }

  /** Attendee
   * Implementation of getTeacherDetailsBySchoolCode
   * @return [type]          Mix
   */
  public function getTeacherDetailsBySchoolCode() {
    $schoolCode = $_REQUEST['schoolCode'];
    $editId = $_REQUEST['editId'];
    $masterDataService = \Drupal::service('sewing.master_data');
    $userData = $masterDataService->getTeacherDataBySchoolCode($schoolCode, $editId);
    $return = ['data' => $userData, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation of deleteTeacherUser
   * @return [type]          Mix
   */
  public function deleteTeacherUser() {
    $nid = $_REQUEST['nid'];
    $return = ['data' => $nid, STATUS => 1];
    if(!empty($nid)) {
      $node = \Drupal\node\Entity\Node::load($nid); 
      $node->set(STATUS, 0);
      $node->save();
      drupal_set_message(t('Teacher has been deleted successfully.'), 'status');      
    }
    return new JsonResponse($return);
  }
  
  /**
   * Implementation Show Notification 
   */
  public function sewingShowNotification($id) {
     $result = \Drupal::database()->select('custom_sewing_notifications', 'n')
          ->fields('n', array('sender_role', 'receiver_role', 'message'))->condition('id', $id)
            ->execute()->fetchAll(\PDO::FETCH_OBJ);

      $status_update = \Drupal::database()->update('custom_sewing_notifications')->fields(['status' => 2, ])->condition('id', $id)->execute();

      foreach ($result as $row => $content) {
        $message = $content->message;
      }

    return [
            '#title' => 'Notification',
            '#theme' => 'notification_board',
            '#message' => $message
           ];
  }

  /**
   * Delete Notification
   * @params: $id as integer
   * @return: Boolean value
  */
  public function sewingDeleteNotification() {
    $database = \Drupal::database();
    $id = $_REQUEST['id'];
    $database->delete('custom_sewing_notifications')
    ->condition('id', $id)
    ->execute(); 
    $return = ['data' => [], STATUS => 1];
    drupal_set_message(t('Notification deleted successfully.'), 'status');
    return new JsonResponse($return);
  }


  /**
   * Implementation of getSewingCoursesList
   * @param  [type] $schoolId int
   * @return [type]          Mix
   */
  public function getSewingCoursesList($schoolId) {
    if($schoolId != '_none') {
      $masterDataService = \Drupal::service('sewing.master_data');
      $coursesList = $masterDataService->getSewingCourses($schoolId);
    } else {
      $coursesList = [];
    }
    $return = ['data' => $coursesList, STATUS => 1];
    return new JsonResponse($return);
  } 
	# 
	public function queryForStudentFeeFunction(){
		$masterDataService = \Drupal::service('sewing.master_data');
		//$studentIds = [185676];
		 $studentIds =\Drupal::entityQuery('node')
			->condition('type', 'manage_sewing_students')
			->execute();
		//die('hello'); 
		$i = 1;
		foreach($studentIds as $studentId){
			$studentData = Node::load($studentId);
			//print_r($studentData);
			//die;
			if(empty($studentData->field_sewing_exam_result->value)){
				$studentData->set('field_sewing_exam_result', 3);
				$studentData->save();
				echo 'Sno:- '.$i.' And Student Id:- '. $studentId;
				echo '<br>';
				$i++;
			}
		}
		print_r('Done');

		/* By Prashant $schoolIDs =\Drupal::entityQuery('node')
		->condition('type', 'sewing_school')
		->condition('nid', 91993 )
		->execute();
		
		$Snid = Node::load(91993);
		print_r($Snid->field_sewing_school_code->value); */
		/* die('die command'); 
		$studentIds =\Drupal::entityQuery('node')
			->condition('type', 'manage_sewing_students')
			->condition('field_sewing_exam_result', 1)
			->condition('field_sewing_certificate_issued', 1)
			->condition('field_sewing_course_fee_out', 0, '>=')
			->execute();
			print_r(count($studentIds));
			echo '<br>'; */
		/* print_r($studentIds);
		die('hello'); */
		/* $i = 1;
		foreach($studentIds as $studentId){
			$studentData = Node::load($studentId);
			$studentData->set('field_student_status', 0);
			$studentData->set('field_sewing_exit_code', 58);
			$studentData->set('status', 1);
			$studentData->save();
			echo 'Sno:- '.$i.'And Student Id:- '. $studentId;
			echo '<br>';
			$i++;
		}
		print_r('Done'); */
		die;
	}
	#
	public function getSSIUserByLocation($locationId){
	$userIds = \Drupal::entityQuery('user')
		  ->condition('status', 1)
		  ->condition('roles', 'sewing_ssi')
		  ->condition('field_user_location', $locationId)
		  ->execute();
	
		$userNodes = \Drupal\user\Entity\User::loadMultiple($userIds);
		foreach($userNodes as $userNode) {
			$userNodeArray[$userNode->id()] = $userNode->getUsername();
		} 
		$return = ['data' => $userNodeArray, STATUS => 1];    
		return new JsonResponse($return);
	}
  #
}







