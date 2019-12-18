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
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
/**
 * Defines HelloController class.
 */
class SewingSchoolController extends ControllerBase {
  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The AcceptLearnersController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
  public function __construct(FormBuilder $formBuilder) {
    $this->formBuilder = $formBuilder;
  }
	
  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */ 
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }
  /**
 * sewingWeeklyPjpPlan function.
 */
  	public function sewingWeeklyPjpPlan() {
	  	$build = [];
	  	$build['heading'] = [ HASH_TYPE => 'markup'];
	  	$build['form'] = $this->formBuilder()->getForm('Drupal\sewing_school\Form\sewingWeeklyPjpPlanForm');
	  	return $build;
	}
	/**
 * sewingChangePassword function.
 */
  	public function sewingChangePassword() {
		$modalTitle = 'Change Password';
		$response = new AjaxResponse(); 
		// Get the modal form using the form builder.
		$modal_form = $this->formBuilder->getForm('Drupal\sewing_school\Form\sewingChangePasswordForm');
		// Add an AJAX command to open a modal dialog with the form as the content.
		$response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));
		return $response;
	  	/* $build = [];
	  	$build['heading'] = [ HASH_TYPE => 'markup'];
	  	$build['form'] = $this->formBuilder()->getForm('Drupal\sewing_school\Form\sewingChangePasswordForm');
	  	return $build; */
	}
  # Get Dealer Details for dealer Select
  public function getSchoolDealerDetails(){
    $filterField = \Drupal::request()->request; 
    $dealerNid = $filterField->get('dealerNid');

    $feeData = Node::load($dealerNid);
    $dealerCode = $feeData->field_dealer_code->value;
    $dealerName = $feeData->getTitle();
    // $dealerCode = $feeData->field_dealer_code->value;
    $dealerAddressStreet = $feeData->field_street->value;
    $dealerAddressLocality = $feeData->field_locality->value;
    $dealerStatus = $feeData->status->value;
    if (isset($dealerStatus)) {
      if ($dealerStatus == 0) {
        $status = 'Inactive';
      } 
      else
        $status = 'Active';
    }
    $data = 'Dealer Name: '.$dealerName. ', Dealer Address: '.$dealerAddressStreet.' '.$dealerAddressLocality.', Dealer Status: '.$status;
   
    $return = ['data' => $data, STATUS => 1]; 
    return new JsonResponse($return);
  }
  # Implement Get course Count By Grade
  public function getCourseCountByGrade(){
    $filterField = \Drupal::request()->request; 
    $gradeNid = $filterField->get('gradeNid');
    $courseIds = \Drupal::entityQuery('node')
            ->condition('type', 'course_master')
            ->condition('field_grade', $gradeNid)
            ->condition(STATUS, 1)
            ->execute();
    $data = count($courseIds);
    $return = ['data' => $data, STATUS => 1]; 
    return new JsonResponse($return);
  }
  # School 'Approval' Status Update
  public function schoolApprovalStatusFlow($nid) { 
      $modalTitle = 'School Approval Form';
      $response = new AjaxResponse(); 
      // Get the modal form using the form builder.
      $modal_form = $this->formBuilder->getForm('Drupal\sewing_school\Form\schoolApprovalStatusFlow', $nid);
      // Add an AJAX command to open a modal dialog with the form as the content.
      $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));
      return $response;
  }
  # School 'Modified Need Approval' Status Update
  public function modifiedSchoolNeedApproval($nid){
    if($nid){
      $node = Node::load($nid);
      $node->set('field_sew_school_approval_status', 1); 
	  $node->set('field_sew_school_edit_remark', ''); 
      $node->save();
      $return = ['data' =>1, STATUS => 1];
      drupal_set_message(t('School has been Approved successfully.'), 'status');
    } 
    return new JsonResponse($return);
  }
  # Bulk School 'Terminate' Status Update
  public function terminatedSchoolState($nid){
    if($nid){
      $node = Node::load($nid);
      if($node->field_sew_school_approval_status->value == 1 || $node->field_sew_school_approval_status->value == 6){
        $teacherNids = \Drupal::entityQuery('node')
            ->condition('type', 'sewing_teacher_management')
            ->condition('field_sewing_school_code_list', $nid)
            ->execute();
        foreach ($teacherNids as $teacherNid) {
          $teacherNode = Node::load($teacherNid);
          $teacherNode->set(STATUS, 0);
          $teacherNode->save();
        }
        $studentNids = \Drupal::entityQuery('node')
          ->condition('type', 'manage_sewing_students')
          ->condition('field_sewing_school_code_list', $nid)
          ->condition('field_student_status', 1)
          ->execute();
        foreach ($studentNids as $studentNid) {
          $studentNode = Node::load($studentNid);
          $studentNode->set('field_student_status', 0);
          $studentNode->set('field_sewing_exit_code', 56);
          $studentNode->set(STATUS, 0);
          $studentNode->save();
        }
        $node = Node::load($nid);
        $userData = User::load($node->field_sewing_user_id->target_id);
        $userData->set(STATUS, 0);
        $userData->save();
        $node->set('field_sew_school_approval_status', 3); 
        $node->set('field_termination_date', date('Y-m-d')); 
        $node->save();
        $return = ['data' =>1, STATUS => 1];
        drupal_set_message(t('School has been Terminated successfully.'), 'status');
      }
    }    
    return new JsonResponse($return);
  }
  # Bulk School 'Terminate' Status Update
  public function schoolTerminatedToApprove($nid){
    if($nid){
      $teacherNids = \Drupal::entityQuery('node')
          ->condition('type', 'sewing_teacher_management')
          ->condition('field_sewing_school_code_list', $nid)
          ->execute();
      foreach ($teacherNids as $teacherNid) {
        $teacherNode = Node::load($teacherNid);
        $teacherNode->set(STATUS, 1);
        $teacherNode->save();
      }
      $studentNids = \Drupal::entityQuery('node')
        ->condition('type', 'manage_sewing_students')
        ->condition('field_sewing_school_code_list', $nid)
        ->condition('field_student_status', 1)
        ->execute();
      foreach ($studentNids as $studentNid) {
        $studentNode = Node::load($studentNid);
        $studentNode->set('field_student_status', 1);
        $studentNode->set('field_sewing_exit_code', '');
        $studentNode->set(STATUS, 1);
        $studentNode->save();
      }
      $node = Node::load($nid);
      $userData = User::load($node->field_sewing_user_id->target_id);
      $userData->set(STATUS, 1);
      $userData->save();
      $node->set('field_sew_school_approval_status', 1); 
      $node->set('field_termination_date', ''); 
      $node->save();
      $return = ['data' =>1, STATUS => 1];
      drupal_set_message(t('School has been Terminated successfully.'), 'status');
    }    
    return new JsonResponse($return);
  }
  # Get Town By Location
  public function getTownByLocation($nid){
    if($nid){
      $townIds = \Drupal::entityQuery('node')
            ->condition('type', 'manage_towns')
            ->condition('field_location', $nid)
            ->condition(STATUS, 1)
            ->execute();
      foreach ($townIds as $townId) {
        $node = Node::load($townId);
        $townList[$node->id()] = $node->getTitle();
      }
      $return = ['data' =>$townList, STATUS => 1];
    }    
    return new JsonResponse($return);
  }
  /*
  * Implement School Bulk Terminate Status Update
  */
  public function bulkSchoolTerminateStatus(){
    $filterField = \Drupal::request()->request; 
    $schoolNids = $filterField->get('schoolNids');
    foreach ($schoolNids as $schoolNid) {
      $node = Node::load($schoolNid);
      if($node->field_sew_school_approval_status->value == 1 || $node->field_sew_school_approval_status->value == 6){
        $teacherNids = \Drupal::entityQuery('node')
          ->condition('type', 'sewing_teacher_management')
          ->condition('field_sewing_school_code_list', $schoolNid)
          ->execute();
        foreach ($teacherNids as $teacherNid) {
          $teacherNode = Node::load($teacherNid);
          $teacherNode->set(STATUS, 0);
          $teacherNode->save();
        }
        $studentNids = \Drupal::entityQuery('node')
          ->condition('type', 'manage_sewing_students')
          ->condition('field_sewing_school_code_list', $schoolNid)
          ->condition('field_student_status', 1)
          ->execute();
        foreach ($studentNids as $studentNid) {
          $studentNode = Node::load($studentNid);
          $studentNode->set('field_student_status', 0);
          $studentNode->set('field_sewing_exit_code', 56);
          $studentNode->set(STATUS, 0);
          $studentNode->save();
        }
        $node = Node::load($schoolNid);
        $userData = User::load($node->field_sewing_user_id->target_id);
        $userData->set(STATUS, 0);
        $userData->save();
        $node->set('field_sew_school_approval_status', 3); 
        $node->set('field_termination_date', date('Y-m-d')); 
        $node->save();
      }
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('School has been Terminated successfully.'), 'status');
    return new JsonResponse($return);
  }
  # Bulk School 'Approve' Status Update
  public function bulkSchoolApproveStatus(){
    $filterField = \Drupal::request()->request; 
    $schoolNids = $filterField->get('schoolNids');
    $schoolNids = array_reverse($schoolNids);
    $schoolNids = array_values($schoolNids);
    foreach ($schoolNids as $schoolNid) {
      $node = Node::load($schoolNid);
      if($node->field_sew_school_approval_status->value == 0){
        $masterDataService = \Drupal::service('sewing.master_data');
        $schoolId = $nid;
        $data = [ 'remarks' => 1 ];
        $approvalStatus = $masterDataService->approveSchool($schoolNid, $data);
      }else if($node->field_sew_school_approval_status->value == 4){
        $node->set('field_sew_school_approval_status', 1); 
        $node->save();
      }
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('School has been Approved successfully.'), 'status');
    return new JsonResponse($return);
  }
  # Bulk School 'On Hold' Status Update
  public function bulkSchoolOnHoldStatus(){
    $filterField = \Drupal::request()->request; 
    $schoolNids = $filterField->get('schoolNids');
    $schoolNids = array_reverse($schoolNids);
    $schoolNids = array_values($schoolNids);
    foreach ($schoolNids as $schoolNid) {
      $node = Node::load($schoolNid);
      if($node->field_sew_school_approval_status->value == 1){
        $node = Node::load($schoolNid);
        $userData = User::load($node->field_sewing_user_id->target_id);
        $userData->set(STATUS, 0);
        $userData->save();
        $node->set('field_sew_school_approval_status', 6); 
        $node->save();
        drupal_set_message(t('School has been On-Hold successfully.'), 'status');
      }
    }
    $return = ['data' =>1, STATUS => 1];
    return new JsonResponse($return);
  }
  # School On Hold to Approve Status
  public function schoolOnHoldToApprove($nid){
    if($nid){
      $schoolNid = $nid;
        $node = Node::load($schoolNid);
        $userData = User::load($node->field_sewing_user_id->target_id);
        $userData->set(STATUS, 1);
        $userData->save();
        $node->set('field_sew_school_approval_status', 1); 
        $node->save();
        drupal_set_message(t('School has been Approve successfully.'), 'status');
      }
      $return = ['data' =>1, STATUS => 1];    
    return new JsonResponse($return);
  }
  public function sewingGalleryPage(){
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load(\Drupal::currentUser()->id());
    if(ROLE_SEWING_SSI == $roles[1]) {
      $userData = User::load($current_user->id());
      $loactionId[] = $userData->field_user_location->target_id; 
      $viewGallery = Views::getView('sewing_gallery_view');
      $viewGallery->setDisplay('page_2');
      $viewGallery->setArguments($loactionId);
      $sewingViewGalleryRender = $viewGallery->render();
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
      $viewGallery = Views::getView('sewing_gallery_view');
      $viewGallery->setDisplay('page_2');
      $viewGallery->setArguments($locationId);
      $sewingViewGalleryRender = $viewGallery->render();
    }else{
      $viewGallery = Views::getView('sewing_gallery_view');
      $viewGallery->setDisplay('page_3');
      $sewingViewGalleryRender = $viewGallery->render();
    }
    return [
            '#title' => 'Event Pictures',
            '#theme' => 'sewing_gallery_page',
            '#sewingViewGalleryRender' => $sewingViewGalleryRender,
        ];
  }
  #Student Bulk Result Update Process
  public function bulkStudentResultUpdate() { 
    $masterDataService = \Drupal::service('sewing.master_data');
    $filterField = \Drupal::request()->request; 
    $examResult = $filterField->get('examResult');
    //$examResultDate = $filterField->get('examResultDate');
    $examResultDate = date('Y-m-d');
    $examResultGrade = $filterField->get('examResultGrade');
    $studentNids = $filterField->get('studentNids');
    foreach ($studentNids as $studentNid) {
      $studentNode = Node::load($studentNid);
      if($studentNode->field_sewing_course_fee_received->value !=0){
        $courseCodeId = $studentNode->field_sewing_course_code_list->target_id;
        $courseData = $masterDataService->getCourseDetailById($courseCodeId);
        $courseDurationNode = Node::load($courseData->field_course_duration->target_id);
        $courseExamRequire = $courseDurationNode->field_exam_required->value;
        if($courseExamRequire == 1){
          $studentNode->set('field_sewing_exam_result', $examResult);
          $studentNode->set('field_sewing_result_date', $examResultDate);
          if($examResult == 1){
            $studentNode->set('field_sewing_grades', $examResultGrade);
          }
          $studentNode->save();
        }
      }
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Result has been successfully Updated.'), 'status');
    return new JsonResponse($return);
  }
  #Student Bulk Raise a Machine Request
  public function bulkStudentRaiseMachineRequest() { 
    $filterField = \Drupal::request()->request; 
    $timeToBuy = $filterField->get('timeToBuy');
    $studentNids = $filterField->get('studentNids');
    foreach ($studentNids as $studentNid) {
      $studentNode = Node::load($studentNid);
      $studentNode->set('field_sewing_want_to_buy_new', 1);
      $studentNode->set('field_sewing_time_to_buy', $timeToBuy);
      $studentNode->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Machine Request has been successfully Updated.'), 'status');
    return new JsonResponse($return);
  }
  #Student Bulk Certificate Issued
  public function bulkStudentCertificateIssued() { 
    $filterField = \Drupal::request()->request; 
    $certificateDate = $filterField->get('certificateDate');
    $studentNids = $filterField->get('studentNids');
    foreach ($studentNids as $studentNid) {
      $studentNode = Node::load($studentNid);
      $studentNode->set('field_sewing_certificate_issued', 1);
      $studentNode->set('field_date_of_certificate_issued', $certificateDate);
      $studentNode->set('field_student_status', 0);
      $studentNode->set('field_sewing_exit_code', 45);
      $studentNode->set('status', 0);
      $studentNode->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Certificate Issued has been successfully Updated.'), 'status');
    return new JsonResponse($return);
  }
  public function bulkStudentCertificatePrint() { 
    $filterField = \Drupal::request()->request; 
    $certificateDate = $filterField->get('certificateDate');
    $studentNids = $filterField->get('studentNids');
    foreach ($studentNids as $studentNid) {
      $studentNode = Node::load($studentNid);
      $studentNode->set('field_sewing_certificate_print', 1);
      $studentNode->set('field_date_of_certificate_print', $certificateDate);
      $studentNode->set('field_sewing_certificate_issued', 2);
      //$studentNode->set('field_student_status', 0);
      //$studentNode->set('field_sewing_exit_code', 45);
      //$studentNode->set('status', 0);
      $studentNode->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Certificate Print has been successfully Updated.'), 'status');
    return new JsonResponse($return);
  }
  #Affilation latter Print
  public function schoolAffiliationLetter($nid){
    $schoolNodeData = Node::load($nid);
    $affiliationDate = $schoolNodeData->field_sewing_affiliation_date->value;
    $renewalDate = date('d/m/Y', strtotime('-1 day', strtotime('+1 year', strtotime($affiliationDate))));
    $dataArray = [
              'nid'=> $nid, 
              'schoolTitle'=> $schoolNodeData->getTitle(), 
              'schoolCode'=> $schoolNodeData->field_sewing_school_code->value, 
              'approvalDate'=> date('d/m/Y', strtotime($schoolNodeData->field_school_approval_date->value)), 
              'affiliationDate'=> date('d/m/Y', strtotime($affiliationDate)), 
              'renewalDate'=> $renewalDate, 
              'grade'=> Node::load($schoolNodeData->field_sewing_grade->target_id)->getTitle(), 
              'address_1'=> $schoolNodeData->field_address_1->value,
              'address_2'=> $schoolNodeData->field_address_2->value,
              'address_3'=> $schoolNodeData->field_address_3->value,
              'proprietorName'=> User::load($schoolNodeData->field_sewing_user_id->target_id)->field_first_name->value,
              'national_sewing_head_name'=> theme_get_setting('national_sewing_head_name'),
            ];
    return [
        '#title' => 'Affiliation Letter',
        '#theme' => 'sewing_school_affiliation_letter',
        '#dataArray' => $dataArray,
    ];
  }
  #Renewal latter Print
  public function schoolRenewalLetter($nid){
    $schoolNodeData = Node::load($nid);
    $renewalPrevDate = date('d/m/Y',  strtotime('-1 year', strtotime($schoolNodeData->field_sewing_date_of_renewal->value)));
    $dataArray = [
              'nid'=> $nid, 
              'schoolTitle'=> $schoolNodeData->getTitle(), 
              'schoolCode'=> $schoolNodeData->field_sewing_school_code->value, 
              'currentDate'=> date('d/m/Y'), 
              'renewalPrevDate'=> $renewalPrevDate, 
              'renewalDate'=> date('d/m/Y', strtotime('-1 day', strtotime($schoolNodeData->field_sewing_date_of_renewal->value))), 
              'grade'=> Node::load($schoolNodeData->field_sewing_grade->target_id)->getTitle(), 
              'address_1'=> $schoolNodeData->field_address_1->value,
              'address_2'=> $schoolNodeData->field_address_2->value,
              'address_3'=> $schoolNodeData->field_address_3->value,
              'proprietorName'=> User::load($schoolNodeData->field_sewing_user_id->target_id)->field_first_name->value,
              'national_sewing_head_name'=> theme_get_setting('national_sewing_head_name'),
            ];
    return [
        '#title' => 'Renewal Letter',
        '#theme' => 'sewing_school_renewal_letter',
        '#dataArray' => $dataArray,
    ];
  }
  #
  #Student Bulk Raise a Machine Request
  public function sewingExportInFolderTest() { 
  //echo 'hello';
 // die;
	file_unmanaged_delete('sites/default/files/custom-export/silai-school-export.csv');
	$view = Views::getView('silai_manage_school');
	$display = $view->preview('data_export_1');
	file_unmanaged_save_data($display , 'sites/default/files/custom-export/silai-school-export.csv', FILE_EXISTS_REPLACE);
    $return = ['data' =>1, STATUS => 1];
    return new JsonResponse($return);
  }
  #
}
