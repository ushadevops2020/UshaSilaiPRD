<?php
namespace Drupal\mis\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\AccessResult; 
use Drupal\Core\Form\FormInterface;
use Drupal\user\Entity\User;

/**
 * Class MISController.
 */
class MISController extends ControllerBase {
	/**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
 	 protected $formBuilder;

  /**
   * The AcceptAgreementController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
	  public function __construct(FormBuilder $formBuilder) {
	    $this->formBuilder = $formBuilder;
	  }
    /**
     * Implementation of Call Master Data Service
    */
    public function initMasterDataService() {
      return $masterDataService = \Drupal::service('silai.master_data');
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
   * Display.
   *
   * @return string
   *   Return Hello string.
   */
    public function display() {
      // $form['from_date'] = array (
      //   HASH_TYPE => 'date',
      //   HASH_TITLE => t('From Date')        
      // );
      // $form['to_date'] = array (
      //   HASH_TYPE => 'date',
      //   HASH_TITLE => t('To Date')        
      // );
      // $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
      // $form[ACTIONS]['search'] = [
      //   HASH_TYPE => 'submit',
      //   HASH_VALUE => $this->t('Search')
      // ];
      
      $header_table = array(
        'name' => t('PC Name'),
        'week' => t('Week'),
        'from' => t('From Date'),
        'to' => t('To Date'),
        'submission_date' => t('Submission date'),
        'action' => t('Action'),
      );
      $query = \Drupal::database()->select('usha_weekly_mis', 'm');
      $query->fields('m', ['id','pc_uid','week','submission_date']);
      $results = $query->execute()->fetchAll();
      $rows=array();
       foreach($results as $data){
          $userId = $data->pc_uid;
          $user = \Drupal\user\Entity\User::load($userId);
          $name = $user->field_first_name->value;
          $sDate = isset($data->submission_date) ? date('d M, Y', $data->submission_date) : '';
          $delete = Url::fromUserInput('/mydata/form/delete/'.$data->id);
          $edit   = Url::fromUserInput('/add_weekly_mis?destination=/manage-weekly-mis&id='.$data->id);
           $rows[] = array(
            'name' => $name,
            'week' => $data->week,
            'from' => $sDate,
            'to' => $sDate,
            'submission_date' => $sDate,
              \Drupal::l('Edit', $edit),
              \Drupal::l('Delete', $delete) 
          );
        }
      $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No Record found.'),
      ];
      return $form;
    }

    /**
   * Display the markup.
   *
   * @return array
   */
    public function weeklyBulkUpload(Request $request) {

      $form = \Drupal::formBuilder()->getForm('Drupal\mis\Form\WeeklyBulkImportForm');
      
      return $form;
    }

    /**
   * Display the markup.
   *
   * @return array
   */
    public function monthlyBulkUpload(Request $request) {

      $form = \Drupal::formBuilder()->getForm('Drupal\mis\Form\MonthlyBulkImportForm');
      
      return $form;
    }

  /**
   * Get Blocks By District Id
   * @params: $townId as integer
   * @return: $blockList as an array
  */
  public function getBlocksByDistrict($districtId) {
    $masterDataService = $this->initMasterDataService();
    $blockList = $masterDataService->getBlocksByDistrictId($districtId);
    $return = ['data' => $blockList, STATUS => 1];
    return new JsonResponse($return);
  }

  /**
   * Get School Code By Village Id
   * @params: $townId as integer
   * @return: $schoolCode as an array
  */
  public function getSchoolCodeByVillage($villageId) {
    $masterDataService = $this->initMasterDataService();
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $ngoId = '';
    if(in_array(ROLE_SILAI_NGO_ADMIN, $roles)) {
      $ngoIdArr = $masterDataService->getLinkedNgoForUser($current_user->id());
      $ngoId = $ngoIdArr[$current_user->id()];
    } 
    $schoolCodeList = $masterDataService->getSchoolCodeByVillageId($villageId, $ngoId);
    $return = ['data' => $schoolCodeList, STATUS => 1];
    return new JsonResponse($return);
  }
  /**
   * Handler for autocomplete request.
   */
  public function schoolCodeAutocomplete(Request $request, $field_name, $count) {
    $results = [];
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $locationId = $user->field_user_location->target_id;
    if ($input = $request->query->get('q')) {
       $query = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'sewing_school')
        ->condition('field_sew_school_approval_status', APPROVED_STATUS)
        ->condition('field_location', $locationId)
        ->condition('field_sewing_school_code', '%'. db_like($input) . '%', 'LIKE');
      $nids = $query->execute();
      $node_storage = \Drupal::entityManager()->getStorage(NODE);
      $schoolCodes = $node_storage->loadMultiple($nids);
      foreach ($schoolCodes as $n) {
          $results[] = $n->field_sewing_school_code->value;
      }
    }
    return new JsonResponse($results);
  }

  /**
   * Delet Weekly MIS
   * @params: $townId as integer
   * @return: Boolean value
  */
  public function deleteWeeklyMIS() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['id'];
    $database->update('usha_weekly_mis')->fields(array('is_deleted' => 1))->condition('id', $id)->execute();
    $return = ['data' => $results, STATUS => 1];
    drupal_set_message(t('MIS has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }

  # Bulk Monthly/Quarterly MIS Delete
  public function bulkMonthlyMISDeleteProcess(){
    $filterField = \Drupal::request()->request; 
    $monthlyMISids = $filterField->get('monthlyMISids');
    $monthlyMISids = array_reverse($monthlyMISids);
    $monthlyMISids = array_values($monthlyMISids);
    foreach ($monthlyMISids as $monthlyMISid) {
      $database = \Drupal::database();
      $query = $database->delete('usha_monthly_mis')->condition('id', $monthlyMISid)->execute(); 
    }
    $return = ['data' =>1, STATUS => 1];
    drupal_set_message(t('Monthly MIS has been successfully Deleted.'), 'status');
    return new JsonResponse($return);
  }
  /**
   * Delet Monthly/Quarterly MIS
   * @params: $townId as integer
   * @return: Boolean value
  */
  public function deleteMonthlyMIS() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['id'];
    $database->update('usha_monthly_mis')->fields(array('is_deleted' => 1))->condition('id', $id)->execute();
    $return = ['data' => $results, STATUS => 1];
    drupal_set_message(t('MIS has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }

  /**
   * Handler for autocomplete request.
   */
  public function getMonthlyQuarterlyValue() {
    $results = array();
    $fiscalYr = $_POST['fiscalYr'];
    $type = $_POST['type'];
    $schoolCode = $_POST['schoolCode'];
    $misid = $_POST['misid'];
    $misDataService = \Drupal::service('silai_mis.monthly_quarterly_list');
    $results = $misDataService->monthlyQuarterlyList($fiscalYr, $type, $schoolCode, $misid);
    $return = ['data' => $results, STATUS => $type];
    return new JsonResponse($return);
  }

  /**
   * Handler for autocomplete request.
   */
  public function getFiscalYear() {
    $results = array();
    $type = $_POST['type'];
    $schoolCode = $_POST['schoolCode'];
    $misid = $_POST['misid'];
    $misDataService = \Drupal::service('silai_mis.monthly_quarterly_list');
    $results = $misDataService->getMQFiscalYear($type, $schoolCode, $misid);
    $return = ['data' => $results, STATUS => 1];
    return new JsonResponse($return);
  }
  
  /**
   * Delet Sewing Weekly MIS
   * @params: $townId as integer
   * @return: Boolean value
  */
  public function deleteSewingWeeklyMIS() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['id'];
    $database->update('usha_sewing_weekly_mis')->fields(array('is_deleted' => 1))->condition('id', $id)->execute();
    $return = ['data' => $results, STATUS => 1];
    drupal_set_message(t('MIS has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }

  /**
   * Display the markup.
   *
   * @return array
   */
    public function sewingWeeklyBulkUpload(Request $request) {
      $form = \Drupal::formBuilder()->getForm('Drupal\mis\Form\SewingWeeklyBulkImportForm');
      return $form;
    }
  
}	