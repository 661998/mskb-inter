<?php

namespace Drupal\assign_roll_number\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Mpdf\HTMLParserMode;
use Drupal\file\Entity\File;

/**
 * Action description.
 *
 * @Action(
 *   id = "generate_id_card",
 *   label = @Translation("Generate ID Card"),
 *   type = ""
 * )
 */
class GenerateIdCard extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    // Do some processing..
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    $data = '';
    foreach ($entities as $delta => $entity) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($entity->id());
      $photo = File::load($node->get('field_photo')->target_id);
      if(current($node->get('field_stream')->referencedEntities())->getName() == 'Arts'){
        $honours_subject = $node->get('field_honours_subject_arts')->referencedEntities()[0]->getName();
      }elseif(current($node->get('field_stream')->referencedEntities())->getName() == 'Science'){
        $honours_subject = $node->get('field_honours_subject_science')->referencedEntities()[0]->getName();
      }elseif(current($node->get('field_stream')->referencedEntities())->getName() == 'Commerce'){
        $honours_subject = $node->get('field_honours_subject_commerce')->referencedEntities()[0]->getName();
      }
      $name = $node->getTitle();
      $roll = $node->get('field_class_roll_no')->value;
      $fname = $node->get('field_father_s_name')->value;
      $dob = date('d/m/Y',strtotime(date($node->get('field_date_of_birth')->value.'12:00:00')));
      $mobile = $node->get('field_mobile')->value;

      $image_text = 'Name='.$name.' Class Roll No='.$roll.' Father Name='.$fname.' D.O.B='.$dob.' Mobile No.='.$mobile.' Honours Subject='.$honours_subject;
      $google_text = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($image_text) . "&choe=UTF-8";

      $data .= 
        '<div class="id_wrapper">
          <div class="id_front">
            <div class="front_content_wrapper">
              <h4 class="card__info">IDENTITY CUM LIBRARY CARD</h4>
              <div class="user__name"><span class="id_value_no">Department of '.current($node->get('field_stream')->referencedEntities())->getName().'</span></div>
              <div class="user__name"><span class="id_label_no">Class Roll No.</span><span class="seperator">:</span><span class="id_value_no">'.$node->get('field_class_roll_no')->value.'</span></div>
              <div class="profile__photo" style="background-image: url('.$photo->createFileUrl().');"></div>
              <h2 class="student__name">'.$node->getTitle().'</h2>
              <table width="100%" class="course-details">
                <tr>
                  <td style="width:38%;"><span class="id_label">Hons Subject</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.$honours_subject.'</span></td>
                </tr>
                <tr>
                  <td style="width:38%;"><span class="id_label">Session</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.current($node->get('field_session')->referencedEntities())->getName().'</td>
                </tr>
                <tr>
                  <td style="width:38%;"><span class="id_label">Course</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.current($node->get('field_course')->referencedEntities())->getName().'</span></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="id_back">
            <div class="back_content_wrapper">
              <table width="100%" class="qr-sign">
                <tr>
                  <td align="center"><img src="'.$google_text.'" width="85px" height="85px"/></td>
                  <td align="center">
                    <img src="/themes/custom/allinonecms/images/Signature.png" width="85px" height="auto"/>
                    <p class="p-sign">Principal Sign</p>
                  </td>
                </tr>
              </table>
              <table width="100%" class="s-details">
                <tr>
                  <td style="width:38%;"><span class="id_label">F. Name</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.$node->get('field_father_s_name')->value.'</span></td>
                </tr>
                <tr>
                  <td style="width:38%;"><span class="id_label">D.O.B</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.date('d/m/Y',strtotime(date($node->get('field_date_of_birth')->value.'12:00:00'))).'</span></td>
                </tr>
                <tr>
                  <td style="width:38%;"><span class="id_label">Mobile</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.$node->get('field_mobile')->value.'</span></td>
                </tr>
                <tr>
                  <td style="width:38%;"><span class="id_label">E-mail ID</span></td>
                  <td align="left" style="width:3%;">:</td>
                  <td><span class="id_value">'.$node->get('field_email_id')->value.'</span></td>
                </tr>
              </table>
            </div>
            <div class="bottom_text">If found please deliver to: Office of Principal Dr. RMLS College, Muzaffarpur Diwan Road, Muzaffarpur - 842001</div>
          </div>
        </div>';
        // Process the entity..
    }
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
    $mpdf->AddPageByArray([
        'margin-top' => '15',
        'margin-bottom' => '15',
        'margin-left' => '5',
        'margin-right' => '5',
    ]);
    $stylesheet = file_get_contents('modules/custom/assign_roll_number/css/id_pdf.css');
    $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($data);
    $mpdf->Output('sites/default/files/id_card.pdf', \Mpdf\Output\Destination::FILE);

    \Drupal::messenger()->addMessage(t('ID Card file will be downloaded in <b>5 seconds</b> if not then click <a data-auto-download href="@url"><b>here</b></a> to download.', ['@url' => 'sites/default/files/id_card.pdf']));
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE);
    return $return_as_object ? $result : $result->isAllowed();
  }

}