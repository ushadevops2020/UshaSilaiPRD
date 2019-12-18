<?php

namespace Drupal\sewing\Controller;

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
    $userId = $_REQUEST[USERID];
    $refId = $_REQUEST[REFID];
    $modalTitle = ($userId || $refId) ? 'Accept Inventory' : LABEL_FORWARD_INVENTORY;
    $response = new AjaxResponse();
    
    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\sewing\Form\AcceptInventoryForm');

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));

    return $response;
  }

  /**
   * Callback for opening forward inventory form.
   */
  public function openForwardInventoryForm() {
    $userId = $_REQUEST[USERID];
    $nid = $_REQUEST['nid'];
    $modalTitle = ($userId) ? LABEL_FORWARD_INVENTORY : LABEL_FORWARD_INVENTORY;
    $response = new AjaxResponse();
    
    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\sewing\Form\ForwardInventoryForm');

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));

    return $response;
  }


  /**
   * Callback for opening forward inventory form.
   */
  public function viewForwardedItems() {
    $nid = $_REQUEST['nid'];
     
    if(isset($_REQUEST[REFID])) {
      $refId = ($_REQUEST[REFID]) ? $_REQUEST[REFID] : 0;
    } else {
      $userId = ($_REQUEST[USERID]) ? $_REQUEST[USERID] : 0;
    }
    $destinationData = drupal_get_destination();
    // Get a node storage object.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    // Load a single node.
    $sentInventoryData = $node_storage->load($nid);
    $forwardedItems = [];
    $database = \Drupal::database();
    $connection = Database::getConnection();
    
      $sentItemQty = $sentInventoryData->field_sewing_inv_quantity->value;
      $currentUserId = \Drupal::currentUser()->id();

       $getAdminSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND, QTY_RECEIVED, TOTAL_FORWARDED))->condition('nid', $nid)->condition('receiver_id', $currentUserId);
        $getAdminSentItemData = $getAdminSentItemqry->execute();
        $adminSentItems = $getAdminSentItemData->fetchAll(\PDO::FETCH_OBJ);
        $receivedItems = $adminSentItems[0]->qty_received;

      $ssiForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND, REF_ID, 'receiver_id'))->fields('i', array('title'));
      $ssiForwardedItemqry->leftjoin('node_field_data', 'i', 'i.nid = n.ref_id');
      $ssiForwardedItemqry->condition('n.nid', $nid)->condition('n.parent_ref_id', $currentUserId)->condition('n.sender_role', ROLE_SEWING_SSI);
      $ssiForwardedItemData = $ssiForwardedItemqry->execute();
      $ssiForwardedItems = $ssiForwardedItemData->fetchAll(\PDO::FETCH_OBJ);
      $forwardedItems = $ssiForwardedItems;
   
    $sentItem = $sentInventoryData->field_sewing_item_name->target_id;
    // Load a single node.
    $sentItemData = $node_storage->load($sentItem);
    
   return [
            '#title' => 'Forwarded Inventory Detail',
            '#theme' => 'forwarded_sewing_inventory_detail',
            '#sent_item' => ($sentItemData) ? $sentItemData->title->value : '',
            '#sent_item_qty' => $receivedItems,
            '#forwarded_items' => $forwardedItems,
            '#ref_id' => ($refId) ? $refId : 0
            
        ];

  }
}