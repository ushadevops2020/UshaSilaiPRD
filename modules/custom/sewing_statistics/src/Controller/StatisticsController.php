<?php

namespace Drupal\sewing_statistics\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;
//use PHPExcel\PHPExcel;




/**
 * Defines StatisticsController class.
 */   
class StatisticsController extends ControllerBase {

  /**
   * School Revenue report
   */
  public function schoolRevenue() { 
      $action = $_REQUEST['action'];
      $masterDataService = \Drupal::service('location_master.master_data');
      $sewingMasterDataService = \Drupal::service('sewing_statistics.master_data');
      $locationListArr = $schoolTypeArr = array('_none' => '--Select--');
      $locationList = $masterDataService->getLocationByCountryId();
      if(!empty($locationList)) {
        $locationListArr = $locationListArr + $locationList;
      }
      $schoolTypeList = $sewingMasterDataService->getSchoolTypeList();
      if(!empty($schoolTypeList)) {
        $schoolTypeArr = $schoolTypeArr + $schoolTypeList;
      }
      $optionsArr['action'] = 'export';
      if($_REQUEST['location'])  {
        $optionsArr['location'] = $_REQUEST['location'];
      }
      if($_REQUEST['schoolType'])  {
        $optionsArr['schoolType'] = $_REQUEST['schoolType'];
      }
      if($_REQUEST['from_date'])  {
        $optionsArr['from_date'] = $_REQUEST['from_date'];
      }
      if($_REQUEST['to_date'])  {
        $optionsArr['to_date'] = $_REQUEST['to_date'];
      }            
      $form = [];
      $form['form'] = [
          '#type'  => 'form',
      ];

      $form['form']['filters'] = [
          '#type'  => 'fieldset',
          '#open'  => true,
      ];

      $form['form']['export_link'] = [
          '#title' => $this->t('Export'),
          '#type'  => 'link',
          '#url' => Url::fromRoute('sewing_statistics.school-revenue-report', $optionsArr),
          HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
      ];
      $form['form']['filters']['location'] = [
          '#title'         => $this->t('Location'),
          '#type'          => 'select',
          '#name'          => 'location',
          '#value' => $_REQUEST['location'],
          '#options'       => $locationListArr,
          
      ];

      $form['form']['filters']['schoolType'] = [
          '#title'         => $this->t('School Type'),
          '#type'          => 'select',
          '#name'          => 'schoolType',
          '#value' => $_REQUEST['schoolType'],
          '#options'       => $schoolTypeArr,
          
      ];

      $form['form']['filters']['from_date'] = [
          '#title'         => $this->t('From Date'),
          '#type'          => 'date',
          '#name'          => 'from_date',
          '#value' => $_REQUEST['from_date'],
      ];
      $form['form']['filters']['to_date'] = [
          '#title'         => $this->t('To Date'),
          '#type'          => 'date',
          '#name'          => 'to_date',
          '#value' => $_REQUEST['to_date'],
      ];      
      $form['form']['filters']['actions'] = [
          '#type'       => 'actions'
      ];
      $form['form']['filters']['actions'][HASH_PREFIX] = '<div class="filter-btn">';
      $form['form']['filters']['actions']['submit'] = [
          '#type'  => 'submit',
          '#value' => $this->t('Search'),
      ];  
      $form['form']['filters']['actions'][HASH_SUFFIX] = '</div>';

 

      $form['form']['filters']['actions']['reset'] = array(
          '#type' => 'button',
          '#button_type' => 'reset',
          '#value' => $this->t('Reset'),
          '#validate' => array(),
          '#attributes' => array(
                'onclick' => 'resetPage(1);',
              ),
      );      
      $header = [
         'location' => t('Location'),
         'schoolCode' => t('School Code'),
         'schoolName' => t('Name Of School'),
         'schoolType' => t('School Type'),
         'town' => t('Town'),
         'prospectusIssued' => t('Prospectus issued'),
         'numberOfAdmission' => t('No. of Admission'),
       ];

     $masterDataService = \Drupal::service('sewing_statistics.master_data');
     $revenueHeads = $masterDataService->getRevenueHeadList();
        foreach ($revenueHeads as $key => $value) {
          $headerKey = preg_replace('/\s+/', '', $value);
          $header['fee'.$headerKey] = t($value);
        }
      $header['totalRevenue'] = t('Total Revenue (Rs.)');
      $header['serviceTax'] = t('Service Tax');
      $header['netAmount'] = t('Net Amt');

    if($action && $action == 'export') {

      $masterDataService = \Drupal::service('sewing_statistics.master_data');
      $schoolNodes =  $masterDataService->getSewingSchoolsNodes($_REQUEST);
      
      $excelReport =  $masterDataService->createSchoolWiseRevenueExcel($schoolNodes, $_REQUEST);

        exit;
    } else {

      $masterDataService = \Drupal::service('sewing_statistics.master_data');
      $schoolNodes =  $masterDataService->getSewingSchoolsNodes($_REQUEST);

    
    foreach($schoolNodes as $node) {
      $locationData = Node::load($node->field_location->target_id);
      $locationName = $locationData->getTitle();

      $townData = Node::load($node->field_town_city->target_id);
      $townName = $townData->getTitle();

      $masterDataService = \Drupal::service('sewing_statistics.master_data');
      $admissionCounts =  $masterDataService->getSchoolTotalStudents($node->id());
      $schoolFee =  $masterDataService->getSchoolFee($node->id(), $_REQUEST);
      $schoolTypeData = Node::load($node->field_sewing_school_type->target_id);
      $schoolType = $schoolTypeData->getTitle();

      // $totalRevenue = ceil($schoolFee[$node->id()][REVENUE_HEAD_STUDENT_FEE_NID]->fee_value + $schoolFee[$node->id()][REVENUE_HEAD_RENEWAL_FEE_NID]->fee_value + $schoolFee[$node->id()][REVENUE_HEAD_AFFILIATION_FEE_NID]->fee_value + $schoolFee[$node->id()][REVENUE_HEAD_PROSPECTUS_FEE_NID]->fee_value);

      // $totalTax = ceil($schoolFee[$node->id()][REVENUE_HEAD_STUDENT_FEE_NID]->tax + $schoolFee[$node->id()][REVENUE_HEAD_RENEWAL_FEE_NID]->tax + $schoolFee[$node->id()][REVENUE_HEAD_AFFILIATION_FEE_NID]->tax + $schoolFee[$node->id()][REVENUE_HEAD_PROSPECTUS_FEE_NID]->tax);

      $output[$node->id()] = [
            'location' => $locationName,
            'schoolCode' => $node->field_sewing_school_code->value,
            'schoolName' => $node->getTitle(),
            'schoolType' => $schoolType,
            'town' => $townName,
            'prospectusIssued' => '',
            'numberOfAdmission' => count($admissionCounts),
          ];

          $totalRevenue = 0;
          $totalTax = 0;
          foreach ($revenueHeads as $key => $value) {
          
          if($key == REVENUE_HEAD_STUDENT_FEE_NID) {
              $schoolStudentFee = $masterDataService->getSchoolStudentTypeFee($node->id(), $key, $_REQUEST);

            $feeValue = ($schoolStudentFee[$node->id()][$key]->total_student_fee) ? $schoolStudentFee[$node->id()][$key]->total_student_fee : 0;
            $output[$node->id()]['fee'.$value] = $feeValue;
            $totalRevenue = ceil($totalRevenue + $schoolStudentFee[$node->id()][$key]->total_student_fee);
            $totalTax = ceil($totalTax + $schoolStudentFee[$node->id()][$key]->tax);
          } else {
            $feeValue = ($schoolFee[$node->id()][$key]->fee_value) ? $schoolFee[$node->id()][$key]->fee_value : 0;
            $output[$node->id()]['fee'.$value] = $feeValue;
            $totalRevenue = ceil($totalRevenue + $schoolFee[$node->id()][$key]->fee_value);
            $totalTax = ceil($totalTax + $schoolFee[$node->id()][$key]->tax);
          }
          
        }
            // 'studentFee' => $schoolFee[$node->id()][REVENUE_HEAD_STUDENT_FEE_NID]->fee_value,
            // 'renewalFee' => $schoolFee[$node->id()][REVENUE_HEAD_RENEWAL_FEE_NID]->fee_value,
            // 'affiliationFee' => $schoolFee[$node->id()][REVENUE_HEAD_AFFILIATION_FEE_NID]->fee_value,
            // 'prospectusFee' => $schoolFee[$node->id()][REVENUE_HEAD_PROSPECTUS_FEE_NID]->fee_value,
            // 'othersFee' => '',
            // 'affiliationFormFee' => '',
          //   'totalRevenue' => $totalRevenue,
          //   'serviceTax' => $totalTax,
          //   'netAmount' => ceil($totalRevenue - $totalTax),
          // ];

          $output[$node->id()]['totalRevenue'] = $totalRevenue;
          $output[$node->id()]['serviceTax'] = $totalTax;
          $output[$node->id()]['netAmount'] = ceil($totalRevenue - $totalTax);
      //$schoolList[$node->id()] = $node->getTitle();
    }
    
    $form['table'] = [
                  '#type' => 'table',
                  '#header' => $header,
                  '#rows' => $output,
                  '#empty' => t('No Result found'),
                  ];
                  
    $form['pager'] = array(
                      '#type' => 'pager'
                    );
                    

    return $form;  
    }                          
  } 


  public function workShopActivityReport() { 
      $action = $_REQUEST['action'];
      $masterDataService = \Drupal::service('location_master.master_data');
      $locationListArr = array('_none' => '--Select--');
      $locationList = $masterDataService->getLocationByCountryId();
      if(!empty($locationList)) {
        $locationListArr = $locationListArr + $locationList;
      }

      $optionsArr['action'] = 'export';
      if($_REQUEST['location'])  {
        $optionsArr['location'] = $_REQUEST['location'];
      }
      if($_REQUEST['from_date'])  {
        $optionsArr['from_date'] = $_REQUEST['from_date'];
      }
      if($_REQUEST['to_date'])  {
        $optionsArr['to_date'] = $_REQUEST['to_date'];
      }            
      $form = [];
      $form['form'] = [
          '#type'  => 'form',
      ];

      $form['form']['filters'] = [
          '#type'  => 'fieldset',
          '#open'  => true,
      ];

      $form['form']['export_link'] = [
          '#title' => $this->t('Export'),
          '#type'  => 'link',
          '#url' => Url::fromRoute('sewing_statistics.workshop-activity-report', $optionsArr),
          HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
      ];
      $form['form']['filters']['location'] = [
          '#title'         => $this->t('Location'),
          '#type'          => 'select',
          '#name'          => 'location',
          '#value' => $_REQUEST['location'],
          '#options'       => $locationListArr,
          
      ];
      // $form['form']['filters']['from_date'] = [
      //     '#title'         => $this->t('From Date'),
      //     '#type'          => 'date',
      //     '#name'          => 'from_date',
      //     '#value' => $_REQUEST['from_date'],
      // ];
      // $form['form']['filters']['to_date'] = [
      //     '#title'         => $this->t('To Date'),
      //     '#type'          => 'date',
      //     '#name'          => 'to_date',
      //     '#value' => $_REQUEST['to_date'],
      // ];      
      $form['form']['filters']['actions'] = [
          '#type'       => 'actions'
      ];
      $form['form']['filters']['actions'][HASH_PREFIX] = '<div class="filter-btn">';
      
      $form['form']['filters']['actions']['submit'] = [
          '#type'  => 'submit',
          '#value' => $this->t('Search'),
          //HASH_ATTRIBUTES => [CLASS_CONST => ['location-search']]
      ];  
      $form['form']['filters']['actions'][HASH_SUFFIX] = '</div>';
      $form['form']['filters']['actions']['reset'] = array(
          '#type' => 'button',
          '#button_type' => 'reset',
          '#value' => $this->t('Reset'),
          '#validate' => array(),
          '#attributes' => array(
                'onclick' => 'resetPage(2);',
              ),
      );
      $header = [
       'location' => t('Location'),
       'schoolCode' => t('Count - SCHOOL CODE'),
       'noOfAttendees' => t('Sum - FOOTFALL'),
       'noOfSchool' => t('Sum - CONFIRMED NO.OF SCHOOL ADMISSION'),
       'noOfSale' => t('Sum - CONFIRMED NO.OF SALE'),
       'noOfProspectusGen' => t('Sum - Prospects generated for Sale'),
     ];


    if($action && $action == 'export') {

      $masterDataService = \Drupal::service('sewing_statistics.master_data');
      $workShopActivityNodes =  $masterDataService->getWorkshopAndActivity($_REQUEST);
      
      $excelReport =  $masterDataService->createLocationWiseWActivityExcel($workShopActivityNodes, $_REQUEST);

        exit;
    } else {

      $masterDataService = \Drupal::service('sewing_statistics.master_data');
      $workShopActivityNodes =  $masterDataService->getWorkshopAndActivity($_REQUEST);
      
    foreach($workShopActivityNodes as $key => $node) {
      $locationId = $key;
      $locationData = Node::load($locationId);
      $locationName = $locationData->getTitle();
      
      $masterDataService = \Drupal::service('sewing_statistics.master_data');
      $wActivityData =  $masterDataService->getWActivityData($locationId);
      
      $schoolCount = Count($wActivityData[$locationId]['schoolCount']);
      $attendees = $wActivityData[$locationId]['attendees'];
      $numberOfSchools = $wActivityData[$locationId]['numberOfSchools'];
      $numberOfSale = $wActivityData[$locationId]['numberOfSale'];
      $prospectusGenSale = $wActivityData[$locationId]['prospectusGenSale'];

      

      $output[$locationId] = [
            'location' => $locationName,
            'schoolCode' => $schoolCount,
            'noOfAttendees' => $attendees,
            'noOfSchool' => $numberOfSchools,
            'noOfSale' => $numberOfSale,
            'noOfProspectusGen' => $prospectusGenSale,
          ];
      //$schoolList[$node->id()] = $node->getTitle();
    }
    
    $form['table'] = [
                  '#type' => 'table',
                  '#header' => $header,
                  '#rows' => $output,
                  '#empty' => t('No Result found'),
                  ];
                  
    $form['pager'] = array(
                      '#type' => 'pager'
                    );
                    

    return $form;  
    }
  }


  public function schoolTypeWiseReport()
  {
    $action = $_REQUEST['action'];

    # Start filter form
    $masterDataService = \Drupal::service('location_master.master_data');
      $locationListArr = array('_none' => '--Select--');
      $locationList = $masterDataService->getLocationByCountryId();
      if(!empty($locationList)) {
        $locationListArr = $locationListArr + $locationList;
      }

      $optionsArr['action'] = 'export';
      if($_REQUEST['location'])  {
        $optionsArr['location'] = $_REQUEST['location'];
      }
      if($_REQUEST['from_date'])  {
        $optionsArr['from_date'] = $_REQUEST['from_date'];
      }
      if($_REQUEST['to_date'])  {
        $optionsArr['to_date'] = $_REQUEST['to_date'];
      }            
      $form = [];
      $form['form'] = [
          '#type'  => 'form',
      ];

      $form['form']['filters'] = [
          '#type'  => 'fieldset',
          '#open'  => true,
      ];

      $form['form']['export_link'] = [
          '#title' => $this->t('Export'),
          '#type'  => 'link',
          '#url' => Url::fromRoute('sewing_statistics.sewing_school_type wise_report', $optionsArr),
          HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
      ];
      $form['form']['filters']['location'] = [
          '#title'         => $this->t('Location'),
          '#type'          => 'select',
          '#name'          => 'location',
          '#value' => $_REQUEST['location'],
          '#options'       => $locationListArr,
          
      ];
      $form['form']['filters']['from_date'] = [
          '#title'         => $this->t('From Date'),
          '#type'          => 'date',
          '#name'          => 'from_date',
          '#value' => $_REQUEST['from_date'],
      ];
      $form['form']['filters']['to_date'] = [
          '#title'         => $this->t('To Date'),
          '#type'          => 'date',
          '#name'          => 'to_date',
          '#value' => $_REQUEST['to_date'],
      ];      
      $form['form']['filters']['actions'] = [
          '#type'       => 'actions'
      ];
      $form['form']['filters']['actions'][HASH_PREFIX] = '<div class="filter-btn">';
      
      $form['form']['filters']['actions']['submit'] = [
          '#type'  => 'submit',
          '#value' => $this->t('Search'),
          //HASH_ATTRIBUTES => [CLASS_CONST => ['location-search']]
      ];  
      $form['form']['filters']['actions'][HASH_SUFFIX] = '</div>';
      $form['form']['filters']['actions']['reset'] = array(
          '#type' => 'button',
          '#button_type' => 'reset',
          '#value' => $this->t('Reset'),
          '#validate' => array(),
          '#attributes' => array(
                'onclick' => 'resetPage(3);',
              ),
      );
      # END filter form

    $header = [
       'location' => t('Location Code'),
       'locationName' => t('Location Name'),
     ];
    $masterDataService = \Drupal::service('sewing.master_data');
    $statisticsMasterDataService = \Drupal::service('sewing_statistics.master_data');

    //print_r($_REQUEST);die;
     # location Ids listing 
    if($_REQUEST && $_REQUEST['location'] && $_REQUEST['location'] != UNDERSCORE_NONE) {
      $locationIds = [$_REQUEST['location']];
      $LocationIds =  $masterDataService->getLocationList($_REQUEST['location']);
    } else {
      $LocationIds =  $masterDataService->getLocationList();
    }
    
    $schoolTypeList =  $statisticsMasterDataService->getSchoolTypeList($_REQUEST);
    $revenueHeads = $statisticsMasterDataService->getRevenueHeadList();

    foreach ($schoolTypeList as $key => $value) {
      $headerName = preg_replace('/\s+/', '', $value);
      $header[$headerName] = strtoupper('NO OF '.$value.' Schools');
    }
    
    
      foreach ($revenueHeads as $row => $data) {
        foreach ($schoolTypeList as $key => $value) {
          $headerKey = preg_replace('/\s+/', '', $value);
          $headerValue = explode(' ', $value);
          
          $header[$data.$headerKey] = strtoupper('Total '.$data.' Received '. $headerValue[0].' Schools');
        }
      }
      $header['othersfee'] = t('OTHERS FEE');
      $header['totalAmt'] = t('TOTAL AMT');
      $header['servicetax'] = t('SERVICE TAX');
      $header['netAmt'] = t('NET AMT');
    
    foreach ($schoolTypeList as $key => $value) {
      $headerName = preg_replace('/\s+/', '', $value);
      $header[$headerName.$key] = strtoupper('TOTAL ENROLEMENTS IN '.$value.' Schools');
    }

    $output = [];

    // /$LocationIds =  $masterDataService->getLocationList();
    //$schoolTypeList =  $statisticsMasterDataService->getSchoolTypeList();

    if($action && $action == 'export') {
      $excelReport =  $statisticsMasterDataService->createLSTWiseReport($LocationIds, $revenueHeads, $schoolTypeList, $_REQUEST);

        exit;
    } else {
      if(!empty($LocationIds)) {
        #Iterate through locations
        foreach ($LocationIds as $key => $value) {
          $output[$key]['location'] = $value['code'];
          $output[$key]['locationName'] = $value['name'];

          #Iterate through School Type List for getting schooltypeCount
          foreach ($schoolTypeList as $row => $data) {
            $locationWiseSchoolTypes = $statisticsMasterDataService->getLocationWiseSchoolTypeCount($key, $row);
            $schoolTypeKeyName  = preg_replace('/\s+/', '', $data);
            $output[$key][$schoolTypeKeyName] = $locationWiseSchoolTypes;
          }
          $totalAmount = 0;
          $totalServiceTax = 0;
          foreach ($revenueHeads as $row1 => $data1) {
            foreach ($schoolTypeList as $key1 => $value1) {
              $headerKey = preg_replace('/\s+/', '', $value['name'].$data1.$value1);
              if($row1 == REVENUE_HEAD_STUDENT_FEE_NID) {
                $feeReceived = $statisticsMasterDataService->getStudentTypeReceivedFee($row1, $key1, $key, $_REQUEST);
                $totalAmount += $feeReceived[$row1][$key1]->fee_value;
                $totalServiceTax += $feeReceived[$row1][$key1]->tax;
                
                $output[$key][$headerKey] = $feeReceived[$row1][$key1]->fee_value;
              } else {
                $feeReceived = $statisticsMasterDataService->getReceivedFee($row1, $key1, $key, $_REQUEST);
                $totalAmount += $feeReceived[$row1][$key1]->fee_value;
                $totalServiceTax += $feeReceived[$row1][$key1]->tax;
                
                $output[$key][$headerKey] = $feeReceived[$row1][$key1]->fee_value;
              }
            }
          }

          $output[$key]['othersfee'] = '';
          $output[$key]['totalAmt'] = ceil($totalAmount);
          $output[$key]['servicetax'] = ceil($totalServiceTax);
          $output[$key]['netAmt'] = ceil($totalAmount - $totalServiceTax);

          foreach ($schoolTypeList as $key2 => $value2) {
            $headerName = preg_replace('/\s+/', '', $value2);
            $studentsEnroled = $statisticsMasterDataService->getSchoolTypeWiseEnrolements($key2, $key);
            $output[$key][$headerName.$key2] = ($studentsEnroled) ? $studentsEnroled : 0; 
          }

        }
      }
    }
    
    $form['table'] = [
                  '#type' => 'table',
                  '#header' => $header,
                  '#rows' => $output,
                  '#empty' => t('No Result found'),
                  ];
                  
    $form['pager'] = array(
                      '#type' => 'pager'
                    );
                    

    return $form;

  }

}
