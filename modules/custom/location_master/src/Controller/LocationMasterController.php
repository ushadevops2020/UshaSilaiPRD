<?php
namespace Drupal\location_master\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

/**
 * Class LocationMasterController.
 */
class LocationMasterController extends ControllerBase {
  
  /**
   * Implementation of Call Master Data Service
  */
  public function initMasterDataService() {
    return $masterDataService = \Drupal::service('location_master.master_data');
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
   * Implementation of delete User 
   */
  public function deleteUser() {
    $userId = $_POST['userId'];
    $return = ['data' => $townList, STATUS => 1];
    if($userId) {
      user_delete($userId);
      drupal_set_message(t('User has been deleted successfully.'), 'status');      
    }
    return new JsonResponse($return);
  }


   /**
   * Implementation of getDistrictsByLocation
   * @params: $state_id as integer
   * @return string
   */
  public function getDistrictsByLocation($locationId) {
    $masterDataService = $this->initMasterDataService();
    $districtList = $masterDataService->getDistrictsByLocationId($locationId);
    $return = ['data' => $districtList, STATUS => 1];
    return new JsonResponse($return); 
  }

}
