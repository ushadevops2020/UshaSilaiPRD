<?php

namespace Drupal\silai\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBuilder;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
/**
 * Class SilaiController.
 */
class SilaiController extends ControllerBase {
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
   * Implementation of Call Master Data Service
  */
  public function initMasterDataService() {
    return $masterDataService = \Drupal::service('silai.master_data');
  }

  /**
    * Implementation of GetLocationsByCountry Ajax Call
    * @params: $country_id as integer
    *  @return string
  */
  public function getLocationsByCountry($country_id) {
      $masterDataService = $this->initMasterDataService();
      $locationList = $masterDataService->getLocationByCountryId($country_id);
      $return = ['data' => $locationList, STATUS => 1];
      return new JsonResponse($return);
  }
  
   /**
    * Implementation of getStatesByLocation
    * @params: $location_id as integer
    * @return string
   */
  public function getStatesByLocation($location_id) {
      $masterDataService = $this->initMasterDataService();
      $stateList = $masterDataService->getStatesByLocationId($location_id);
      $return = ['data' => $stateList, STATUS => 1];
      return new JsonResponse($return);
  }
  
  /**
   * Implementation of getDistrictsByState
   * @params: $state_id as integer
   * @return string
   */
  public function getDistrictsByState($state_id) {
    $masterDataService = $this->initMasterDataService();
    $districtList = $masterDataService->getDistrictsByStateId($state_id);
    $return = ['data' => $districtList, STATUS => 1];
    return new JsonResponse($return);
  }


  /**
   * Implementation of getTownsByDistrict
   * @params: $district_id as integer
   * @return string
   */
  public function getTownsByDistrict($district_id) {
    $masterDataService = $this->initMasterDataService();
    $townList = $masterDataService->getTownsByDistrictId($district_id);
    $return = ['data' => $townList, STATUS => 1];
    return new JsonResponse($return);
  }


  /**
   * Implementation of getTownsByDistrictName
   * @params: $district_id as integer
   * @return string
   */
  public function getTownsByDistrictName($district) {
    $districtRawData = explode('(', $district);
    $districtData = explode(')', $districtRawData[1]);
    $districtId = (int)trim($districtData[0]);
    $masterDataService = $this->initMasterDataService();
    $townList = $masterDataService->getTownsByDistrictId($districtId);
    $return = ['data' => $townList, STATUS => 1];
    return new JsonResponse($return); 
  }


  /**
   * Implementation of getBlocksByTown
   * @params: $town_id as integer
   * @return string
   */
  public function getBlocksByTown($town_id) {
    $masterDataService = $this->initMasterDataService();
    $blockList = $masterDataService->getBlocksByTownId($town_id);
    $return = ['data' => $blockList, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation of getVillagesByBlock
   * @params: $town_id as integer
   * @return string
   */
  public function getVillagesByBlock($block_id) {
    $masterDataService = $this->initMasterDataService();
    $villageList = $masterDataService->getVillagesByBlockId($block_id);
    $return = ['data' => $villageList, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation of getNgoByLocation
   * @param  [type] $town_id int
   * @return [type]          Mix
   */
  public function getNgoByLocation($locationId) {
    $masterDataService = $this->initMasterDataService();
    $ngoList = $masterDataService->getNgoByLocationId($locationId);
    $return = ['data' => $ngoList, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation of getSchoolsByNgo
   * @param  [type] $town_id int
   * @return [type]          Mix
   */
  public function getSchoolsByNgo($ngoId) {
    $masterDataService = $this->initMasterDataService();
    $schoolList = $masterDataService->getSchoolsByNgoId($ngoId);
    $return = ['data' => $schoolList, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation of getSchoolsByNgo
   * @param  [type] $town_id int
   * @return [type]          Mix
   */
  public function getItemsByItemGroup($itemGroupId) {
    $masterDataService = $this->initMasterDataService();
    $itemList = $masterDataService->getItemsByItemGroup($itemGroupId);
    $return = ['data' => $itemList, STATUS => 1];
    return new JsonResponse($return);
  }


  /**
   * Implementation of Approve School 
   */
  public function approveSchool() {
    $schoolId = $_POST['schoolId'];
    if($schoolId) {
      $masterDataService = $this->initMasterDataService();
      $approvalStatus = $masterDataService->approveSchool($schoolId);
        drupal_set_message(t('School has been Approved successfully.'), 'status');      
    }
    $return = ['data' => $schoolId, STATUS => 1];
    return new JsonResponse($return);
  }


  /**
   * Implementation of Reject School 
   */
  public function rejectSchool() {
    $schoolId = $_POST['schoolId'];
    if($schoolId) {
      $masterDataService = $this->initMasterDataService();
      $rejectionStatus = $masterDataService->rejectSchool($schoolId);
      drupal_set_message(t('School has been Rejected.'), 'status');      
    }
    $return = ['data' => $schoolId, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Implementation Show Notification 
   */
  public function showNotification($id) {
     $result = \Drupal::database()->select('custom_notifications', 'n')
          ->fields('n', array('sender_role', 'receiver_role', 'message'))->condition('id', $id)
            ->execute()->fetchAll(\PDO::FETCH_OBJ);

      $status_update = \Drupal::database()->update('custom_notifications')->fields(['status' => 2, ])->condition('id', $id)->execute();

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
  public function deleteSilaiNotification() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['notificationId'];
    $database->delete('custom_notifications')->condition('id', $id)->execute();
    $return = ['data' => [], STATUS => 1];
    drupal_set_message(t('Notification deleted successfully.'), 'status');
    return new JsonResponse($return);
  }


  /**
   * Implementation Gallery 
   */
  public function galleryPage() {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    #Gallery Slider
    if($roles[1] == 'pc'){
      $user = User::load($current_user->id());
      $locationId = $user->field_user_location->target_id;
      $viewGallery = Views::getView('gallery_view');
      $viewGallery->setDisplay('page_2');
      $viewGallery->setArguments(array($locationId));
      $viewGalleryRender = $viewGallery->render();
    }else if($roles[1] == 'ngo_admin'){
      $currentUserid = $current_user->id();
      $masterDataService = \Drupal::service('silai.master_data');
      $getNgoLocationIds = $masterDataService->getNgoLocationIds($currentUserid);
      $viewGallery = Views::getView('gallery_view');
      $viewGallery->setDisplay('page_2');
      $viewGallery->setArguments($getNgoLocationIds);
      $viewGalleryRender = $viewGallery->render();
    }elseif($roles[1] == 'silai_ho_user'){
      $viewGallery = Views::getView('gallery_view');
      $viewGallery->setDisplay('page_3');
      $viewGalleryRender = $viewGallery->render();
    }else{
      $viewGallery = Views::getView('gallery_view');
      $viewGallery->setDisplay('page_1');
      $viewGalleryRender = $viewGallery->render();
    }
    return [
            '#title' => 'Gallery',
            '#theme' => 'gallery_page',
            '#viewGalleryRender' => $viewGalleryRender, 
        ];
  } 
  # Bulk School Approve
  public function bulkSchoolApprovalProcess(){
    $filterField = \Drupal::request()->request; 
    $schoolNids = $filterField->get('schoolNids');
    $schoolNids = array_reverse($schoolNids);
    $schoolNids = array_values($schoolNids);
    foreach ($schoolNids as $schoolNid) {
      $node = Node::load($schoolNid);
      if($node->field_sil_school_approval_status->value == 0){
        $masterDataService = \Drupal::service('silai.master_data');
        $data = [ 'remarks' => '' ];
        $approvalStatus = $masterDataService->approveSchool($schoolNid, $data);
      }
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('School has been Approved successfully.'), 'status');
    return new JsonResponse($return);
  }
  #Bulk School Partially Close
  public function bulkSchoolPartiallyClose(){
    $filterField = \Drupal::request()->request; 
    $schoolNids = $filterField->get('schoolNids');
    $schoolNids = array_reverse($schoolNids);
    $schoolNids = array_values($schoolNids);
      foreach ($schoolNids as $schoolNid) {
        $node = Node::load($schoolNid);
        if($node->field_sil_school_approval_status->value == 1){
          $userData = User::load($node->field_silai_teacher_user_id->value);
          $userData->set(STATUS, 0);
          $userData->save();
          $node->set('field_sil_school_approval_status', 3); 
          $node->save();
        }
     }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('School has been Partially Close successfully.'), 'status');
    return new JsonResponse($return);
  }
   # School Partially close to Approve Status
  public function bulkSchoolPartiallyCloseToApprove($nid){
    if($nid){
      $schoolNid = $nid;
        $node = Node::load($schoolNid);
        $userData = User::load($node->field_silai_teacher_user_id->value);
        $userData->set(STATUS, 1);
        $userData->save();
        $node->set('field_sil_school_approval_status', 1); 
        $node->save();
        drupal_set_message(t('School has been Approve successfully.'), 'status');
      }
      $return = ['data' =>1, STATUS => 1];    
    return new JsonResponse($return);
  }
  #Bulk School Fully Close
  public function bulkSchoolFullyClose(){
    $filterField = \Drupal::request()->request; 
    $schoolNids = $filterField->get('schoolNids');
    $schoolNids = array_reverse($schoolNids);
    $schoolNids = array_values($schoolNids);
    foreach ($schoolNids as $schoolNid) {
      $node = Node::load($schoolNid);
      $userData = User::load($node->field_silai_teacher_user_id->value);
      $userData->set(STATUS, 0);
      $userData->save();
      $node->set('field_sil_school_approval_status', 4); 
      $node->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('School has been Fully Close successfully.'), 'status');
    return new JsonResponse($return);
  }
  # Bulk Learner Inactive
  public function bulkLearnerInactiveProcess(){
    $filterField = \Drupal::request()->request; 
    $learnerNids = $filterField->get('learnerNids');
    $learnerNids = array_reverse($learnerNids);
    $learnerNids = array_values($learnerNids);
    foreach ($learnerNids as $learnerNid) {
      $node = Node::load($learnerNid);
      $node->set(STATUS, 0); 
      $node->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Learner has been Inactive successfully.'), 'status');
    return new JsonResponse($return);
  }
  # Bulk Learner Course Completion date update
  public function bulkLearnerCourseCompletionDate(){
    $filterField = \Drupal::request()->request; 
    $learnerNids = $filterField->get('learnerNids');
    $courseCompletionDate = $filterField->get('courseCompletionDate');
    $learnerNids = array_reverse($learnerNids);
    $learnerNids = array_values($learnerNids);
    foreach ($learnerNids as $learnerNid) {
      $node = Node::load($learnerNid);
      $node->set('field_course_completion_date', $courseCompletionDate); 
      $node->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Learner course completion date has been updated successfully.'), 'status');
    return new JsonResponse($return);
  }
  # Bulk Learner Received Certificate
  public function bulkLearnerReceivedCertificate(){
    $filterField = \Drupal::request()->request; 
    $learnerNids = $filterField->get('learnerNids');
    $receivedCertificate = $filterField->get('receivedCertificate');
    $learnerNids = array_reverse($learnerNids);
    $learnerNids = array_values($learnerNids);
    foreach ($learnerNids as $learnerNid) {
      $node = Node::load($learnerNid);
      $node->set('field_you_received_certificate', $receivedCertificate); 
      $node->save();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Learner Received Certificate has been updated successfully.'), 'status');
    return new JsonResponse($return);
  }
  # Bulk Learner Delete
  public function bulkLearnerDeleteProcess(){
    $filterField = \Drupal::request()->request; 
    $learnerNids = $filterField->get('learnerNids');
    $learnerNids = array_reverse($learnerNids);
    $learnerNids = array_values($learnerNids);
    foreach ($learnerNids as $learnerNid) {
      $node = Node::load($learnerNid);
      $node->delete();
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Learners has been successfully Deleted.'), 'status');
    return new JsonResponse($return);
  }
  /**
   * getSilai school detail By School id
   * @return string
   */
  public function getSchoolDetailById($id) {
    $schoolId = $id;
    $masterDataService = \Drupal::service('silai.master_data');
    $schoolData = $masterDataService->getSchoolDetailById($schoolId);
    
    if($schoolData->field_silai_teacher_user_id->value) {
      $SchoolAdminData = User::load($schoolData->field_silai_teacher_user_id->value);
    }
    $schoolDetail = [
      'id' => $schoolData->id(),
      'schoolName' => $schoolData->title->value,
      'schoolCode' => $schoolData->field_school_code->value,
      'schoolAdminName' => ($SchoolAdminData->field_first_name->value) ? $SchoolAdminData->field_first_name->value : '',
      'schoolAdminContactNo' => ($SchoolAdminData->field_user_contact_no->value) ? $SchoolAdminData->field_user_contact_no->value : '',
      'schoolAdminEmailId' => ($SchoolAdminData->mail->value) ? $SchoolAdminData->mail->value : '',

    ];
    
    $return = ['data' => $schoolDetail, 'status' => 1];
    return new JsonResponse($return);
  }
  public function agreementPaymentAmountEdit($agreement_id, $nid) { 
      $modalTitle = 'Agreement Payment Amount Edit';
      $response = new AjaxResponse(); 
      // Get the modal form using the form builder.
      $modal_form = $this->formBuilder->getForm('Drupal\silai\Form\AgreementPaymentAmountEdit', $agreement_id, $nid);
      // Add an AJAX command to open a modal dialog with the form as the content.
      $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));
      return $response;
  }


  /**
   * Delete SchoolMisData
   * @params: $id as integer
   * @return: Boolean value
  */
  public function deleteSchoolMisData() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['id'];
    $database->delete('silai_mis_school_data')->condition('id', $id)->execute();
    $return = ['data' => [], STATUS => 1];
    drupal_set_message(t('School Mis data deleted successfully.'), 'status');
    return new JsonResponse($return);
  }
}
