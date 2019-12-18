<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\silai_import\Form;
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

class villageBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'silai_village_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/silai_village_data1.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importvillage/',
      '#default_value' => '',
      HASH_REQUIRED => TRUE,
      "#upload_validators"  => array("file_validate_extensions" => array("xls")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );
    $form['actions']['#type'] = 'actions';

    $form[ACTIONS]['cancel'] = array(
      HASH_TYPE => 'button',
      HASH_VALUE => t('Cancel'),
      HASH_WEIGHT => -1,
      HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "'.$cancelRedirectURI['destination'].'"; event.preventDefault();'),
      );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
      '#button_type' => 'primary',
    );
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $error = array();
/* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('import_file');
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
    for ($row = 2; $row <= $highestRow; $row++) {
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      $data = $this->validateImportData($rowData[0], $row);
      if(!empty($data['error'])) {
        $error[] = $data['error'];
      }else{
        $operations[] = ['\Drupal\silai_import\Services\villageBulkImportContent::villageAddImportContentItem', [$data]];
      }
    }

    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\silai_import\Services\villageBulkImportContent::villageAddImportContentItemCallback',
      );
      batch_set($batch);
    }
    if(!empty($error)) {
      $error = array_map('current', $error);
      $this->importLog($error);
      foreach($error as $key=>$value) {
        drupal_set_message(t('Error: '.$value), 'error');
      }
    } 
    #
  }
  public function validateImportData($item, $row) {
    $error = array();
    if(empty($item[0])){
      $error['error'][$row] = $item[0].' - Village Name field is blank. In excel file row number : '.$row;
    }else if(!empty($item[1])){
      $blockQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'silai_blocks')
        ->condition('field_silai_block_code', $item[1]);
      $blocksID = $blockQuery->execute();
      if(empty($blocksID)){
        $error['error'][$row] = $item[1].' - Block Code is not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($blocksID);
        $blockData = Node::load($nid[0]);
        $item[1] = $nid[0];
        $item[10] = $blockData->field_silai_district->target_id;

        if(!preg_match('/^[0-9\b]*$/', $item[2])) {
          $error['error'][$row] = $item[2].' - Pin Code not valid. it contain only numeric value. In excel file row number : '.$row.'.';  
        }
        if(!preg_match('/^[0-9\b]*$/', $item[3])) {
          $error['error'][$row] = $item[3].' - Population not valid. it contain only numeric value. In excel file row number : '.$row.'.';  
        }
        if(!preg_match('/^[0-9\b]*$/', $item[5])) {
          $error['error'][$row] = $item[5].' - No. of Households not valid. it contain only numeric value. In excel file row number : '.$row.'.';  
        }
		if(!empty($item[6])){
			if($item[6] == 'YES'){
				$item[6] = 1;
			}else if($item[6] == 'NO'){
				$item[6] = 0;  
			}
		}
        if(!preg_match('/^[0-9\b]*$/', $item[7])) {
          $error['error'][$row] = $item[7].' - Distance from local NGO(IN KMS.) not valid. it contain only numeric value. In excel file row number : '.$row.'.';  
        }
        if(!preg_match('/^[0-9\b]*$/', $item[8])) {
          $error['error'][$row] = $item[8].' - Distance from Nearest Place (In Kms.) not valid. it contain only numeric value. In excel file row number : '.$row.'.';  
        }

      }
    }else{
      $error['error'][$row] = $item[1].' - Block Code is empty. In excel file row number : '.$row;
    }
    if(empty($error)) {
      $result = $item;
      
    } else {
      $result = $error;
    }
    return $result;
  }

  public function importLog($error) {
    date_default_timezone_set('Asia/Kolkata');
    $logfile = IMPORT_LOG_PATH."/import-village-log_".date("j_M_Y_h_i_s_a").".txt";
    $current = file_get_contents($file);
    foreach ($error as $key => $value) {
      $a = $key+1;
      $current .= $a.') '.$value.''.PHP_EOL;
    }
    file_put_contents($logfile, $current);
    $destination = IMPORT_LOG_PATH;
    $replace = "";
    file_unmanaged_move($logfile, $destination, $replace);
  }
  #
}