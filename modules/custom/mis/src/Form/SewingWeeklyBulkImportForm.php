<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\mis\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\Core\Extension;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;

class SewingWeeklyBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mis_sewing_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/mis_weekly_sample_file.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );

    $form['mis_import_file'] = array(
      HASH_TYPE => 'managed_file',
      HASH_TITLE => t('Upload file here'),
      HASH_UPLOAD_LOCATION => 'public://importmis/',
      HASH_DEFAULT_VALUE => '',
      HASH_REQUIRED => TRUE,
      HASH_UPLOAD_VALIDATORS  => array("file_validate_extensions" => array("xls")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = array(
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t('Upload'),
      HASH_BUTTON_TYPE => 'primary'
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $error = array();
    /* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('mis_import_file');
    /* Load the object of the file by it's fid */
    $file = File::load( $import_file[0] );
    /* Set the status flag permanent of the file object */
    $file->setPermanent();
    /* Save the file in database */
    $file->save();
    $inputFileType = IOFactory::identify($file->getFileUri());
    $objReader   = IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($file->getFileUri());
    $sheet = $objPHPExcel->getSheet(0); 
    $highestRow = $sheet->getHighestRow(); 
    $highestColumn = $sheet->getHighestColumn();
    $duplicateData = array();
    for ($row = 2; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      $data = $this->validateImportData($rowData[0], $row);
      if(!empty($data['error'])) {
        $error[] = $data['error'];
      } else {
        if(!in_array($data[0], $duplicateData)) {
          $finalData = ['\Drupal\mis\Services\sewingMIS::addImportContent', [$data]];
          $operations[] = $finalData;
        } else {
          $date = date("m/d/Y", $data[0]);
          $error['error'][] = $date.' - Week Start Date is not valid/already used in row number '.$row.'.';
        }
        $duplicateData[] = $data[0];
      } 
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\mis\Services\sewingMIS::addImportContentCallback',
      );
      batch_set($batch);
    }
    if(!empty($error)) {
      // $error = array_map('current', $error);
      $error = $this->array_flatten($error);
      foreach($error as $key=>$value) {
        drupal_set_message(t('Error: '.$value), 'error');
      }
    } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('mis_import_file');
    if (empty($import_file)) {
        $form_state->setErrorByName('error', $this->t('Please upload file.'));
    }


  }

  public function validateImportData($item, $row) {
    $error = array();
    $database = Database::getConnection();
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $misDataService = \Drupal::service('silai_mis.sewing_mis');

    if(in_array(ROLE_SEWING_SSI, $roles)){
      $locationId = $user->field_user_location->target_id;
    }
    
    $notMandatoryKey = array(5,7,9,11,14,15,20);
    foreach(SEWING_WEEKLY_MIS_FIELDS_VILIDATION as $key=>$value) {
      if(!in_array($key, $notMandatoryKey) && $item[$key] == '' && ($item[$key] != 0 || $item[$key] != '0')) {
        $error['error'][] = '<strong>'.$value.'</strong> cannot be left blank in row number '.$row.'.';
      }
    }
    if(!empty($item[0]) && !$this->isRealDate($item[0])) {
        $error['error'][] = '<strong>'.$item[0].'</strong> - Week Start Date is not correct format in row number '.$row.'.';
    } else {
      $date = $this->isRealDate($item[0]);
      $fromDate = strtotime($date);

      $weeksData = $misDataService->getSewingWeeks();
      foreach(array_keys($weeksData) as $value) {
        $startDate = explode('@@',$value);
        $startDateArr[] = $startDate[0];
      }
      if(!in_array($fromDate, $startDateArr)) {
        $error['error'][] = '<strong>'.$date.'</strong> - Week Start Date is not valid/already used in row number '.$row.'.';
      } else {
        $toDate = strtotime("+6 day", $fromDate);
        $item[0] = $fromDate;
        $item['endDate'] = $toDate;
      }
    }
    $schoolCodeArray = explode(',', $item[5]);
    /* foreach($schoolCodeArray as $schoolCode) {
      if(!empty($schoolCode)) {
        $query = \Drupal::entityQuery(NODE)
          ->condition(TYPE, 'sewing_school')
          //->condition('field_sew_school_approval_status', APPROVED_STATUS)
          ->condition('field_location', $locationId)
          ->condition('field_sewing_school_code', trim($schoolCode));
        $schoolCodeArr = $query->execute();
        if(empty($schoolCodeArr)) {
            $error['error'][] = '<strong>'.$schoolCode.'</strong> - School Code is not belong to current user location in row number '.$row.'.';
        }
      }  
    } */
    $item['location'] = $locationId;
    if(empty($error)) {
      $item['type'] = 1;
      $result = $item;
    } else {
      $result = $error;
    }
    return $result;
  }

  public function isRealDate($date) {
    if(is_numeric($date)) {
      $date = $this->getDate($date);
    }
    $date = str_replace('/', '-', $date);
    if (false === strtotime($date)) { 
        return false;
    }

    list($year, $month, $day) = explode('-',$date);
    if(checkdate($month, $day, $year)) {
      return $date;
    } else {
      return false;
    }
  }

  public function getDate($excelDate){
    $unixDate = ($excelDate - 25569) * 86400;
    $excelDate = 25569 + ($unixDate / 86400);
    $unixDate = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $unixDate);  
  }

  /**
 * Bulk Import log write
 * @param
 * $node   Object
 * @return
 */

  public function importLog($error) {
    $logfile = IMPORT_LOG_PATH."/import-mis-weekly-log.txt";
    $filesize = filesize($logfile); 
    $filesize = round($filesize / 1024 / 1024, 1);
    $fileContents = file_get_contents($logfile);
    $log = '--------------------------'.PHP_EOL;
    file_put_contents($logfile, $log . $fileContents);
    $message = array();
    foreach($error as $key=>$value) {
      if($filesize > 1) {
        $filename2 = IMPORT_LOG_PATH."/import-mis-weekly-log_" .date("j_M_Y").'.txt';
        rename($logfile, $filename2);
        $log  = date("j M Y h:i A").' - Row No.- '.$key.' - Error- '.$error[$key][0].PHP_EOL;
        $message .= $log;
        $fileContents = file_get_contents($logfile);
        file_put_contents($logfile, $log . $fileContents);
      } else {
        $log  = date("j M Y h:i A").' - Row No.- '.$key.' - Error- '.$error[$key][0].PHP_EOL;
        $message .= $log;
        $fileContents = file_get_contents($logfile);
        file_put_contents($logfile, $log . $fileContents);
      }
    }
  }

  /**
 * Convert a multi-dimensional array into a single-dimensional array.
 * @author Sean Cannon, LitmusBox.com | seanc@litmusbox.com
 * @param  array $array The multi-dimensional array.
 * @return array
 */
  public function array_flatten($array) { 
    if (!is_array($array)) { 
      return false; 
    } 
    $result = array(); 
    foreach ($array as $key => $value) { 
      if (is_array($value)) { 
        $result = array_merge($result, $value); 
      } else { 
        $result[$key] = $value; 
      } 
    }
    return $result; 
  } 

}