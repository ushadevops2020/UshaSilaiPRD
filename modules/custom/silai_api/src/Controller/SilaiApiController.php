<?php

namespace Drupal\silai_api\Controller;

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
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\encrypt\Entity\EncryptionProfile;

/**
 * Class SewingController.
 */
class SilaiApiController extends ControllerBase {

  public function demoApi() {
    $schoolCode = 759;
    $totalLearner = $this->getLearnerForSchool($schoolCode);
    echo "<br/>totalLearner==".$totalLearner;

    $totalLearner1 = $this->getLearnerForSchool($schoolCode, 1);
    echo "<br/>totalLearner1==".$totalLearner1;
    die;
    $keyArr = array(0, 1);
    $connection = Database::getConnection();
    $query = $connection->select('silai_ngo_payment_detail', 'v');
    $query->addExpression('SUM(v.amount)', 'totalAmount');
    //$query->condition('v.payment_mode', 1);
    $query->condition('v.payment_mode', $keyArr, 'IN');
    $query->condition('v.nid', 1311);
    $rs = $query->execute()->fetchObject();   
    echo "<pre>"  ;print_r($rs);die;
    /*
    $connection = Database::getConnection();
    $result = $connection->query("SELECT n.nid, n.title FROM  {node_field_data} n LEFT JOIN {node__field_location} nfl ON n.nid = nfl.entity_id AND nfl.deleted = ':isDeleted'", [":isDeleted" => 0]);
    //$connection->query("SELECT n.nid, n.title FROM  {node_field_data} n LEFT JOIN {node__field_location} nfl ON n.nid = nfl.entity_id AND nfl.deleted = ':isDeleted'", [":isDeleted" => 0]);
    //$result = $connection->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(2); 
    while ($row = $result->fetchAssoc()) {
      echo "<pre>";print_r($row);
    }

    $str = '12345';
    $ert = $this->aes_encrypt($str);
    echo "<br/>ert==".$ert;
    $drt = $this->aes_decrypt($ert);
    echo "<br/>drt==".$drt;
    die;
    $data['pass'] = 'zKe6qlDQNsIHvTs5lTtOCg';
    $password = $this->aes_decrypt($data['pass']);
    echo "<br/>password==".$password;die;
    $this->aes_encrypt('h');
    $connection = Database::getConnection();
    $query = $connection->select('api_version', 'v');
    $query->fields('v', array('version', 'app_url', 'version_start_date', 'mandatory'));
    $query->addField('v', 'version', 'app_url', 'version_start_date');
    $query->condition('v.status', 1);
    $query->range(0, 1);
    $rs = $query->execute()->fetchObject();

    $result[RESPONSE_CODE] = 1;
    $result['version'] = $rs->version;
    $result['app_url'] = $rs->app_url;
    $result['mandatory'] = $rs->mandatory;
    echo "<pre>";print_r($result);die;
    */
  }


  /**
   * loginApi
   * @return string
   */
  public function loginApi(Request $request) {
    $result[RESPONSE_CODE] = 0;
    $deviceId = $request->headers->get('X-CSRF-Token');
    $data = json_decode( $request->getContent(), TRUE );
    $userName = $data['name'] ;
    $pass = $data['pass'];
    $password = $this->aes_decrypt($data['pass']);
    $langCode = $data[LANGUAGE_CODE] ;
    if($langCode == '') {
      $langCode = 'en';
    }

    $result['message'] = LOGIN_UNSUCESS_MSG[$langCode];
    $userObj = user_load_by_name($userName);
    $result[CSRF_TOKEN] = '';
    $result[SESSION_ID] = '';    
    if($userObj) {
      $user = \Drupal\user\Entity\User::load($userObj->get('uid')->value);
      $hashed_password = $user->get('pass')->value;
      $password_hasher = \Drupal::service('password');
      if($password_hasher->check($password, $hashed_password)) {
        $this->user_login_session($user);
        $session = \Drupal::request()->getSession();
        $sessionId = $session->getId();
        $result[CSRF_TOKEN] = $sessionId;
        $result[SESSION_ID] = $sessionId; 
        $authData['uid'] = \Drupal::currentUser()->id();
        $authData['sid'] = $sessionId;
        $authData['token'] = $sessionId;
        $authData['device_id'] = $deviceId;
        $this->authInsert($authData);

        $result[RESPONSE_CODE] = 1;
        $result['message'] = LOGIN_SUCCESS_MSG[$langCode];
        $role =  $user->getRoles();
        $result['result'] = [
            "uid"           => \Drupal::currentUser()->id(),
            "name"          => $user->getUsername(),
            "mail"          => $user->getEmail(),
            "roleName"      => $role[1],
            "firstName"     => $user->field_first_name->value,
            "lastName"      => '',
            "mobile"        => $user->field_user_contact_no->value,
            "location"      => $user->field_user_location->value
          ];      
          
      }      
    } 

    $response = new JsonResponse($result);
    return new JsonResponse($result);
  }

  /**
   * Implementation of user_login_session
   */
  public function user_login_session($account) {
    \Drupal::currentUser()
      ->setAccount($account);
    \Drupal::logger('user')
      ->notice('Session opened for %name.', array(
      '%name' => $account
        ->getUsername(),
    ));

    $account
      ->setLastLoginTime(REQUEST_TIME);
    \Drupal::entityManager()
      ->getStorage('user')
      ->updateLastLoginTimestamp($account);

    \Drupal::service('session')
      ->migrate();
    \Drupal::service('session')
      ->set('uid', $account
      ->id());
  }

  /**
   * Implementation of School Profile API
   */
  public function schoolProfile(Request $request) {
    $token = $request->headers->get('X-CSRF-Token');
    $tokenArr = explode('@@@', $token);
    $sessionId = $tokenArr[1];
    $deviceId = $tokenArr[2];
    $userId = $request->query->get('uid');

    $langCode = $request->query->get(LANGUAGE_CODE);
    if($langCode == '') {
      $langCode = 'en';
    }

    $result[RESPONSE_CODE] = 0;
    $result[CSRF_TOKEN] = ''; 
    $checkAuth = $this->verifyAuth($userId, $sessionId, $deviceId);
    if($checkAuth) {
      $user = \Drupal\user\Entity\User::load($userId);
      if($user) {
        $result[RESPONSE_CODE] = 1;
        $result[CSRF_TOKEN] = $tokenArr[0];
        $result[SESSION_ID] = $sessionId;       
        $masterDataService = \Drupal::service('silai.master_data');
        $schoolId = $masterDataService->getSchoolFromUserId($userId);
        $nodes = \Drupal\node\Entity\Node::load($schoolId);
        $adharNumber = $this->getAdharNumberBySchoolId($schoolId);
        $adharNumber = $this->aes_encrypt($adharNumber);
        if(!$adharNumber) {
          $adharNumber = '';
        }
        $totalLearners = $this->getLearnerForSchool($schoolId);
        $activeLearners = $this->getLearnerForSchool($schoolId, 1);        
        $result['result'] = [
            "uid" => $userId,
            "name" => $user->getUsername(),
            "mail" => $user->getEmail(),
            "firstName" => $user->field_first_name->value,
            "lastName" => '',
            "mobile" =>  $this->aes_encrypt($user->field_user_contact_no->value),
            "location" =>  $user->field_user_location->target_id,
            "school_code" => $nodes->field_school_code->value,
            "date_of_joining" => $nodes->field_date_open_of_silai_school->value,
            "total_learners" => $totalLearners,
            "currently_active" => $activeLearners,
            "aadhar_card" => $adharNumber
        ];      
      }      
    } else {
       $result['message'] = UNAUTHORIZE_USER_MSG[$langCode];
    }

    $response = new JsonResponse($result);
    return new JsonResponse($result);        
  }

  public function getLearnerForSchool($schoolCode, $status = NULL) {
    /*
      $query = \Drupal::entityQuery('node')->condition('type', "silai_learners_manage")->condition('field_silai_school', $schoolCode);
      if($status == 1) {
        $query->condition('status', $status);
      }
      $count = $query->count()->execute();
      */

    $db = Database::getConnection();
    $query = $db->select('node_field_data', 'n');
    $query->join('node__field_silai_school', 's', 's.entity_id = n.nid');
    $query->condition('s.bundle', 'silai_learners_manage');
    $query->condition('n.type', 'silai_learners_manage');
    $query->condition('s.field_silai_school_target_id', $schoolCode);
      if($status == 1) {
        $query->condition('n.status', $status);
      }    
    $num_rows = $query->countQuery()->execute()->fetchField();      
    return $num_rows;
  }


  /**
   * Implementation of Get Adhar Number for School
   */
  public function getAdharNumberBySchoolId($schoolId) {
    $connection = Database::getConnection();
    $query = $connection->select('silai_add_school_data', 'n');
    $query->addField('n', 'aadhar_number');
    $query->condition('n.nid', $schoolId);
    return $query->execute()->fetchField();
  }

  /**
   * Implementation fo verify auth
   */
  public function checkIsExistSchoolSurvey($schoolId) {
      $connection = \Drupal::database();
      $query = $connection->select('silai_add_school_data', 'n');
      $query->condition('n.nid', $schoolId);
      $count_query = $query->countQuery();
      $num_rows = $query->countQuery()->execute()->fetchField();
      return $num_rows;   
  }
  /**
   * Implementation of update profile API
   */
  public function updateProfile(Request $request) {
    $token = $request->headers->get('X-CSRF-Token');
    $tokenArr = explode('@@@', $token);
    $sessionId = $tokenArr[1];
    $deviceId = $tokenArr[2];
    $result[RESPONSE_CODE] = 0;
    $result[CSRF_TOKEN] = ''; 

    $data = json_decode( $request->getContent(), TRUE );
    $teacherName = $data['teacherName'];
    $mobileNumber = $data['mobileNumber'];
    if($mobileNumber) {
      $mobileNumber = $this->aes_decrypt($mobileNumber);
    }
    $aadharNo = $data['aadharNo'];
    if($aadharNo) {
      $aadharNo =  $this->aes_decrypt($aadharNo);
    }
    $userId = $data['uid'];

    $langCode = $data[LANGUAGE_CODE] ;
    if($langCode == '') {
      $langCode = 'en';
    }

    $checkAuth = $this->verifyAuth($userId, $sessionId, $deviceId);
    if($checkAuth) {
      $user = \Drupal\user\Entity\User::load($userId);
      $result['message'] = t('There is an issue, Please contact to administrator');  
      if($user) {
        $masterDataService = \Drupal::service('silai.master_data');
        $schoolId = $masterDataService->getSchoolFromUserId($userId);        
        $result[RESPONSE_CODE] = 1;
        $result[CSRF_TOKEN] = $tokenArr[0];
        $result[SESSION_ID] = $sessionId; 
        $fieldsArr = array('aadhar_number' => $aadharNo);
        $checkIsExistSchoolSurvey = $this->checkIsExistSchoolSurvey($schoolId);

        $database = Database::getConnection();
        if($checkIsExistSchoolSurvey) {
          $fieldsArr = array('aadhar_number' => $aadharNo);
          $database->update('silai_add_school_data')->fields($fieldsArr)->condition('nid', $schoolId)->execute();
        } else {
          $fieldsArr = array('nid' => $schoolId, 'have_aadhar_card' =>1, 'aadhar_number' => $aadharNo,  'created' => time());
          $database->insert('silai_add_school_data')->fields($fieldsArr)->execute();
        }

        $result['message'] = PROFILE_UPDATE_MSG[$langCode];
        $user->set('field_user_contact_no', $mobileNumber);
        $user->set('field_first_name', $teacherName);
        $user->save();    
      }
    } else {
       $result['message'] = UNAUTHORIZE_USER_MSG[$langCode];
    } 
    $response = new JsonResponse($result);
    return new JsonResponse($result);     
  }

  /**
   *
   */
  public function schoolInventoryList(Request $request) {
    $token = $request->headers->get('X-CSRF-Token');
    $tokenArr = explode('@@@', $token);
    $sessionId = $tokenArr[1];
    $deviceId = $tokenArr[2];
    $result[RESPONSE_CODE] = 0;
    $result[CSRF_TOKEN] = ''; 

    $userId = $request->query->get('uid');

    $langCode = $request->query->get(LANGUAGE_CODE);
    if($langCode == '') {
      $langCode = 'en';
    }

    $checkAuth = $this->verifyAuth($userId, $sessionId, $deviceId);
    if($checkAuth) {    
      $user = \Drupal\user\Entity\User::load($userId);
      if($user) {
        $customAPIDataService = \Drupal::service('custom_api.get_inventory');
        $result = $customAPIDataService->getInventoryList($userId);
        $result[CSRF_TOKEN] = $tokenArr[0];
        $result[SESSION_ID] = $sessionId;         
      }
    } else {
       $result['message'] = UNAUTHORIZE_USER_MSG[$langCode];
    }        
    $response = new JsonResponse($result);
    return new JsonResponse($result); 
  }

  /**
   * Implementation of Logout API
   */
  public function logoutApi(Request $request) { 
    $token = $request->headers->get('X-CSRF-Token');
    $tokenArr = explode('@@@', $token);
    $sessionId = $tokenArr[1];
    $deviceId = $tokenArr[2];
    $result[RESPONSE_CODE] = 0;
    $result[CSRF_TOKEN] = ''; 

    $userId = $request->query->get('uid');
    $langCode = $request->query->get(LANGUAGE_CODE);
    if($langCode == '') {
      $langCode = 'en';
    }

    $checkAuth = $this->verifyAuth($userId, $sessionId, $deviceId);
    if($checkAuth) {
      $result[RESPONSE_CODE] = 1;
      $this->removeAuth($userId, $sessionId, $deviceId);  
      $this->custom_user_logout($userId);
      user_logout();
      session_destroy();
      $result['message'] = t('User logout successfully');
    } else {
       $result['message'] = UNAUTHORIZE_USER_MSG[$langCode];
    }
    $response = new JsonResponse($result);
    return new JsonResponse($result);    
  }

  /**
   * Implementation of custom user logout
   */
  public function custom_user_logout($userId) {
    $user = \Drupal\user\Entity\User::load($userId);
    \Drupal::logger('user')
      ->notice('Session closed for %name.', array(
      '%name' => $user
        ->getAccountName(),
    ));
    \Drupal::moduleHandler()
      ->invokeAll('user_logout', array(
      $user,
    ));

    \Drupal::service('session_manager')
      ->destroy();
  }

  /**
   * Implementation of Update school Inventory API
   */
  public function updateSchoolInventory(Request $request) {
    $token = $request->headers->get('X-CSRF-Token');
    $tokenArr = explode('@@@', $token);
    $sessionId = $tokenArr[1];
    $deviceId = $tokenArr[2];
    $result[RESPONSE_CODE] = 0;
    $result[CSRF_TOKEN] = ''; 

    $data = json_decode( $request->getContent(), TRUE );
    $qty = $data['qty'];
    $id = $data['id'];
    $userId = $data['uid'];

    $langCode = $data[LANGUAGE_CODE];
    if($langCode == '') {
      $langCode = 'en';
    }

    $checkAuth = $this->verifyAuth($userId, $sessionId, $deviceId);
    if($checkAuth) {
      $user = \Drupal\user\Entity\User::load($userId);    
      if($user) {
        $result[RESPONSE_CODE] = 1;
        $result[CSRF_TOKEN] = $tokenArr[0];
        $result[SESSION_ID] = $sessionId;
        $database = Database::getConnection();
        $roles = $user->getRoles();
        $fieldsArr = array('qty_received' => $qty, 'receiver_role' => $roles[1], 'received_date' => time());
        $database->update('custom_manage_inventory')->fields($fieldsArr)->condition('id', $id)->execute();
        $result['message'] = UPDATE_SCHOOL_INVENTORY_MSG[$langCode];   
      }
    } else {
       $result['message'] = UNAUTHORIZE_USER_MSG[$langCode];
    }      
    $response = new JsonResponse($result);
    return new JsonResponse($result);       
  }


  /**
   * Implementation of insertMisdata
   */
  function insertMisdata(Request $request) {
    $token = $request->headers->get('X-CSRF-Token');
    $tokenArr = explode('@@@', $token);
    $sessionId = $tokenArr[1];
    $deviceId = $tokenArr[2];
    $result[RESPONSE_CODE] = 0;
    $result[CSRF_TOKEN] = ''; 
       
    $data = json_decode( $request->getContent(), TRUE );

    $langCode = $data[LANGUAGE_CODE];
    if($langCode == '') {
      $langCode = 'en';
    }

        
    $userId = $data['uid'] ;
    $schoolStatus = $data['schoolStatus'] ;
    $totalLearner = $data['totalLearner'] ;
    if($totalLearner == '') {
      $totalLearner = 0;
    }

    $courseCompleteLearner = $data['courseCompleteLearner'] ;
    if($courseCompleteLearner == '') {
      $courseCompleteLearner = 0;
    } 

    $monthlyIncome = $data['monthlyIncome'] ;
    if($monthlyIncome == '') {
      $monthlyIncome = 0;
    } 

    $machineWorkingStatus = $data['machineWorkingStatus'] ;
    if($machineWorkingStatus == '') {
      $machineWorkingStatus = 3;
    }

    $reason = $data['reason'] ;
    $tmpCloseReason = $data['tmpCloseReason'] ;
    $permCloseReason = $data['permCloseReason'] ;

    $checkAuth = $this->verifyAuth($userId, $sessionId, $deviceId);
    if($checkAuth) {
      $user = \Drupal\user\Entity\User::load($userId);    
      if($user) {
        $database = Database::getConnection();
        $dataArr['school_status'] = $schoolStatus;
        $dataArr['user_id'] = $userId;
        $dataArr['total_learner'] = $totalLearner;
        $dataArr['course_complete_learner'] = $courseCompleteLearner;
        $dataArr['monthly_income'] = $monthlyIncome;
        $dataArr['machine_working_status'] = $machineWorkingStatus;
        $dataArr['reason'] = $reason;
        $dataArr['temporary_close_reason'] = $tmpCloseReason;
        $dataArr['permanently_close_reason'] = $permCloseReason; 
        $dataArr['created'] = time();
        $database->insert('silai_mis_school_data')->fields($dataArr)->execute();
        $result[RESPONSE_CODE] = 1;
        $result[CSRF_TOKEN] = $tokenArr[0];
        $result[SESSION_ID] = $sessionId;
        $result['message'] = MIS_SUCCESS_MSG[$langCode];    
      }
    } else {
       $result['message'] = UNAUTHORIZE_USER_MSG[$langCode];
    }      
    $response = new JsonResponse($result);
    return new JsonResponse($result);           
  }

  /**
   * Implementation of Auth data Insert
   */
  public function authInsert($authData) {
      $sessionId=\Drupal\Component\Utility\Crypt::hashBase64($authData['sid']);
      $database = Database::getConnection();
      $dataArr['user_id'] = $authData['uid'];
      $dataArr[SESSION_ID] = $sessionId;
      $dataArr['token'] = $authData['token'];
      $dataArr['device_id'] = $authData['device_id'];
      $dataArr['created'] = time();
      $database->insert('silai_user_auth')->fields($dataArr)->execute();    
  }

  /**
   * Implementation fo verify auth
   */
  public function verifyAuth($userId, $sessionId, $deviceId) {
        $sessionId=\Drupal\Component\Utility\Crypt::hashBase64($sessionId);
        $database = Database::getConnection();
        $query = $database->select('silai_user_auth', 'a');
        $query->condition('user_id', $userId);
        $query->condition(SESSION_ID, $sessionId);
        $query->condition('device_id', $deviceId);
        return $query->countQuery()->execute();
  }


  /**
   * Implementation of remove auth
   */
  public function removeAuth($userId, $sessionId, $deviceId) {
    $sessionId=\Drupal\Component\Utility\Crypt::hashBase64($sessionId);
    $database = Database::getConnection();
    $database->delete('silai_user_auth')
      ->condition('user_id', $userId)
      ->condition(SESSION_ID, $sessionId)
      ->condition('device_id', $deviceId)
      ->execute();

    $database->delete('sessions')
      ->condition('uid', $userId)
      ->condition('sid', $sessionId)
      ->execute();
  }

  /**
   *
   */
  public function appGetAPIDetail(Request $request) {
    $connection = Database::getConnection();
    $query = $connection->select('api_version', 'v');
    $query->fields('v', array('version', 'app_url', 'version_start_date', 'mandatory'));
    $query->addField('v', 'version', 'app_url', 'version_start_date');
    $query->condition('v.status', 1);
    $query->range(0, 1);
    $rs = $query->execute()->fetchObject();

    $result[RESPONSE_CODE] = 1; 
    $result['result'] = [
        "version" => $rs->version,
        "app_url" => $rs->app_url,
        "mandatory" => $rs->mandatory
    ];
    $response = new JsonResponse($result);
    return new JsonResponse($result);    
  }

  /** 
  * AES Encryption 
  * @param        $code   The code 
  * @return        
  */

  public function aes_decrypt($code) {
    if($code != "") {
      $secretKey  = \Drupal::config(AES_CRED)->get('key');
      $secretIv   = \Drupal::config(AES_CRED)->get('iv');
      $method   = \Drupal::config(AES_CRED)->get('method');
      $data     = $this->hex2bin2($code);
      $data     = openssl_decrypt($data, $method, $secretKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $secretIv);
      $string   = utf8_encode(trim($data));
      return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }
  }


  /** 
   *  Hext2Bin: Supportive function of aes_decrypt 
   *  @param      string  $hexdata  The hexdata 
   *  @return     string  encoded string 
   */

  public function hex2bin2($hexdata) {
     $bindata = '';    
     for ($i = 0; $i < strlen($hexdata); $i += 2) {
      $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
     }    
     return $bindata;
  }

  /** 
   * AES Encrytion 
   * @param        $str    The string 
   * @return       encrypted code 
   */

   public function aes_encrypt($str) {
    $secretKey  = \Drupal::config(AES_CRED)->get('key');
    $secretIv   = \Drupal::config(AES_CRED)->get('iv');
    $method   = \Drupal::config(AES_CRED)->get('method');
    $data = openssl_encrypt($str, $method, $secretKey, OPENSSL_RAW_DATA, $secretIv);
    return bin2hex($data);
   }
}
