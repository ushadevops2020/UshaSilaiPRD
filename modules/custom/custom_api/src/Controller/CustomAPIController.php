<?php 
/**
 * @file
 * Contains \Drupal\custom_api\Controller\CustomAPIController.
 */

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\Component\Serialization\Json;

/**
 * Controller routines for custom_api routes.
 */
class CustomAPIController extends ControllerBase {

  /**
 * Allow access for logged-in, authenticated users.
 *
 * @param \Drupal\Core\Session\AccountInterface $account
 *   Run access checks for this account.
 *
 * @return bool
 *   Return true or false on the basis of criteria specified.
 */

  /**
   * Callback for `usha-api/get.json` API method.
   */
  public function get( Request $request ) {
    $response = array();
    // if (strpos($request->headers->get('Content-Type'), 'application/json') !== 0) {
    //   $res = new JsonResponse();
    //   $res->setStatusCode(400, 'application/json');
    //   return $res;
    // }

    $currentUser = \Drupal::currentUser(); 
    $customAPIDataService = \Drupal::service('custom_api.get_inventory');
    $validation = $customAPIDataService->apiValidation($currentUser, $request);
    if(!empty($validation)) {
      return new JsonResponse($validation);
    } else {
      $getRequestType = $request->query->get('type');
      $queryUId = $request->query->get('uid');
      if($getRequestType == 'inventory' ) {
        $response = $customAPIDataService->getInventoryList($queryUId);
      }elseif($getRequestType == 'user_profile' ) {
        $response = $customAPIDataService->getUserProfile($queryUId);
      }
    }
    
    return new JsonResponse( $response );
  }

  /**
   * Callback for `usha-api/put.json` API method.
   */
  public function put( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'PUT';

    return new JsonResponse( $response );
  }

  /**
   * Callback for `usha-api/post.json` API method.
   */
  public function post( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      // $request->request->replace( is_array( $data ) ? $data : [] );
    }

    $error = $this->validate($request);
    if ($error !== TRUE) {
      return new JsonResponse($error, 400);
    }
    $response = array();
    $currentUser = \Drupal::currentUser(); 
    $customAPIDataService = \Drupal::service('custom_api.get_inventory');
    $validation = $customAPIDataService->apiValidation($currentUser, $request);
    if(!empty($validation)) {
      return new JsonResponse($validation);
    } else {
      $getRequestType = $request->query->get('type');
      if($getRequestType == 'user_update') {
          $response = $customAPIDataService->updateUserProfileAPI($request);
      }
    }  
    return new JsonResponse( $response );
  }

  /**
   * Callback for `usha-api/delete.json` API method.
   */
  public function delete( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'DELETE';

    return new JsonResponse( $response );
  }

  /**
 * Validate the POST data.
 */
  private function validate(Request $request) {
  $error = '';
  // Material Id is mandatory.
  if ($request->request->get('metadata') == '') {
    // $error = t('Invalid post data, metadata was missing.');
    // return $error;
  }
    return TRUE;
  }
}

?>