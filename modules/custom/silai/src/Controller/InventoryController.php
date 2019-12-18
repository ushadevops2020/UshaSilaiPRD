<?php

namespace Drupal\silai\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;

/**
 * InventoryController class.
 */
class InventoryController extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The InventoryController constructor.
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
   * Callback for opening accept inventory form.
   */
  public function openAcceptInventoryForm() {
    $userId = $_GET[USERID];
    $refId = $_GET[REFID];
    $modalTitle = ($userId || $refId) ? 'Accept Inventory' : LABEL_FORWARD_INVENTORY;
    $response = new AjaxResponse();
    
    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\silai\Form\AcceptInventoryForm');

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));

    return $response;
  }

  /**
   * Callback for opening forward inventory form.
   */
  public function openForwardInventoryForm() {
    $userId = $_GET[USERID];
    $nid = $_GET['nid'];
    $modalTitle = ($userId) ? LABEL_FORWARD_INVENTORY : LABEL_FORWARD_INVENTORY;
    $response = new AjaxResponse();
    
    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\silai\Form\ForwardInventoryForm');

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));

    return $response;
  }


  /**
   * Callback for opening forward inventory form.
   */
  public function viewForwardedItems() {
    $nid = $_GET['nid'];
     
    if(isset($_GET[REFID])) {
      $refId = ($_GET[REFID]) ? $_GET[REFID] : 0;
    } else {
      $userId = ($_GET[USERID]) ? $_GET[USERID] : 0;
    }
    $destinationData = drupal_get_destination();
    // Get a node storage object.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    // Load a single node.
    $sentInventoryData = $node_storage->load($nid);
    $forwardedItems = [];
    $database = \Drupal::database();
    $connection = Database::getConnection();
    if($refId) {
      
      $getPcSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, 'qty_received', 'total_forwarded'))->condition('nid', $nid)->condition(REF_ID, $refId);
      $getPcSentItemData = $getPcSentItemqry->execute();
      $pcSendItems = $getPcSentItemData->fetchAll(\PDO::FETCH_OBJ);
      $sentItemQty = $pcSendItems[0]->qty_send;
      $itemsRemaiming = $pcSendItems[0]->qty_received - $pcSendItems[0]->total_forwarded;

      $ngoForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, REF_ID, 'receiver_id'))->condition('nid', $nid)->condition('parent_ref_id', $refId)->condition('sender_role', ROLE_SILAI_NGO_ADMIN);
      $ngoForwardedItemData = $ngoForwardedItemqry->execute();
      $ngoForwardedItems = $ngoForwardedItemData->fetchAll(\PDO::FETCH_OBJ);
      $forwardedItems = $ngoForwardedItems;
      
    } else {
      $sentItemQty = $sentInventoryData->field_silai_quantity->value;
      $currentUserId = \Drupal::currentUser()->id();
      $pcForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, REF_ID, 'receiver_id'))->fields('i', array('title'));
      $pcForwardedItemqry->leftjoin('node_field_data', 'i', 'i.nid = n.ref_id');
      $pcForwardedItemqry->condition('n.nid', $nid)->condition('n.parent_ref_id', $currentUserId)->condition('n.sender_role', ROLE_SILAI_PC);
      $pcForwardedItemData = $pcForwardedItemqry->execute();
      $pcForwardedItems = $pcForwardedItemData->fetchAll(\PDO::FETCH_OBJ);
      $forwardedItems = $pcForwardedItems;
      
    }

    $sentItem = $sentInventoryData->field_silai_item_name->target_id;
    // Load a single node.
    $sentItemData = $node_storage->load($sentItem);
    $masterDataService = \Drupal::service('silai.master_data');
    
   return [
            '#title' => 'Forwarded Inventory Detail',
            '#theme' => 'forwarded_inventory_detail',
            '#sent_item' => ($sentItemData) ? $sentItemData->title->value : '',
            '#sent_item_qty' => $sentItemQty,
            '#forwarded_items' => $forwardedItems,
            '#ref_id' => ($refId) ? $refId : 0
            
        ];

  }
}