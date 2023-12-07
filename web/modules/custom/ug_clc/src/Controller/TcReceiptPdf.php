<?php

namespace Drupal\ug_clc\Controller;

use Drupal\Core\Url;
use Mpdf\HTMLParserMode;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TcReceiptPdf.
 */
class TcReceiptPdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {
    $col_7 = 0;
    $node = Node::load($nid);
    $table_field = $node->get('field_subject_marks_details')->value;
    unset($table_field['caption']);

    $fee_paid_till_date = date("d-M-Y",strtotime($node->get('field_fee_paid_till')->value));
    $date_duration_start = date("d-M-Y",strtotime($node->get('field_date_duration')->value));
    $date_duration_end = date("d-M-Y",strtotime($node->get('field_date_duration')->end_value));
    $session = Term::load($node->get('field_session')->target_id)->get('name')->value;
    $stream = Term::load($node->get('field_stream')->target_id)->get('name')->value;

    $data = '
      <table width="100%">
        <tr>
          <td colspan="4" align="center" style="padding-bottom:30px;"><h2>Transfer Certificate Apply Receipt</h2></td>
        </tr>
        <tr>
          <td colspan="2"><h3>Personal Details</h3></td>
          <th colspan="2" align="right">Date of Submission: '.date('d/m/Y', $node->get('created')->value).'</th>
        </tr>
        <tr>
          <td width="25%"><b>Name of Student: </b></td>
          <td width="25%">'.$node->getTitle().'</td>
          <td width="25%"><b>Session: </b></td>
          <td width="25%">'.$session.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Stream: </b></td>
          <td width="25%">'.$stream.'</td>
          <td width="25%"><b>Character: </b></td>
          <td width="25%">'.$node->get('field_character')->value.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Father\'s Name: </b></td>
          <td width="25%">'.$node->get('field_father_s_name')->value.'</td>
          <td width="25%"><b>Fee paid till: </b></td>
          <td width="25%">'.$fee_paid_till_date.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Mother\'s Name: </b></td>
          <td width="25%">'.$node->get('field_mother_s_name')->value.'</td>
          <td width="25%"><b>Date Duration: </b></td>
          <td width="25%">'. $date_duration_start .' - '. $date_duration_end .'</td>
        </tr>
        <tr>
          <td width="25%"><b>Mobile: </b></td>
          <td width="25%">'.$node->get('field_mobile')->value.'</td>
          <td width="25%"><b>Permanent Address: </b></td>
          <td width="25%">'.$node->get('field_permanent_address')->getValue()[0]['address_line1'].', '.
          $node->get('field_permanent_address')->getValue()[0]['locality'].', '.
          $node->get('field_permanent_address')->getValue()[0]['administrative_area'].', '.
          $node->get('field_permanent_address')->getValue()[0]['postal_code'].'</td>
        </tr>
      </table>';

    $data .= '
      <h3>Subject and Marks details</h3>
      <table class="table-field">';
        foreach($table_field as $row_key => $row) {
          unset($row['weight']);
          $data .= '<tr>';
          foreach($row as $col_key => $column) {
            if($col_key != 6){
              $data .= ($row_key == 0) ? '<th align="left">' : '<td align="left">';
              $data .= $column;        
              $data .= ($row_key == 0) ? '</th>' : '</td>';
            }
            
            // Check 7th column value is there or not.
            if($col_key == 6 && $row_key == 0 && !empty($column)) {
              $col_7 = 1;
            }

            if($col_key == 6 && $col_7){
              $data .= ($row_key == 0) ? '<th align="left">' : '<td align="left">';
              $data .= $column;        
              $data .= ($row_key == 0) ? '</th>' : '</td>';
            }
          }
          $data .= '</tr>';
        }
    $data .= '</table>';


    $data .= '
      <table width="100%">
        <tr>
          <td colspan="4" class="pt-3"><h3>Payment Details</h3></td>
        </tr>
        <tr>
          <td width="25%"><b>UTR/Ref. Number: </b></td>
          <td width="25%">'.$node->get('field_utr_ref_number')->value.'</td>
          <td width="25%"><b>Payment Amount: </b></td>
          <td width="25%">Rs.'.$node->get('field_payment_amount')->value.'/-</td>
        </tr>
        <tr>
          <td width="25%"><b>Date of Payment: </b></td>
          <td width="25%">'.date('d/m/Y',strtotime(date($node->get('field_payment_date')->value.'12:00:00'))).'</td>
        </tr>
      </table>
    ';

    $footer ='
    <table width="100%">
      <tr>
        <td width="50%" align="center" class="py-5"><strong>Signature of Assistant</strong></td>
        <td width="50%" align="center" class="py-5"><strong>Principal\'s Order</strong></td>
      </tr>
    </table>
    ';
    
    
    global $base_url;
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $mpdf->SetHeader('|<img src="'.$base_url.'/themes/custom/allinonecms/images/allinonecms_pdf_logo.png" />|');
    $mpdf->AddPageByArray([
        'margin-top' => '37',
        'margin-bottom' => '15',
    ]);
    $stylesheet = file_get_contents('modules/custom/ug_clc/css/clcreceiptpdf.css');
    $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->SetHTMLFooter($footer);
    $mpdf->WriteHTML($data);
    $mpdf->Output($node->getTitle().'.pdf', 'D');

    return new Response(
        'pdf generated success', 
         Response::HTTP_OK
    );
  }

}
