<?php

namespace Drupal\ug_clc\Controller;

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Class TransferCertificatePdf.
 */
class TransferCertificatePdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {
    $col_7 = 0;
    $node = Node::load($nid);
    $issue_date = $node->get('field_date_of_issue')->value;
    $issue_date = date("d/m/Y",strtotime($issue_date));
    $tc_no = $node->get('field_certificate_number')->value;

    $name = $node->getTitle();
    $f_name = $node->get('field_father_s_name')->value;
    $m_name = $node->get('field_mother_s_name')->value;
    $vill = $node->get('field_permanent_address')->getValue()[0]['address_line1'];
    $locality = $node->get('field_permanent_address')->getValue()[0]['locality'];
    $fee_paid_till_date = date("M Y",strtotime($node->get('field_fee_paid_till')->value));
    $date_duration_start = date("Y",strtotime($node->get('field_date_duration')->value));
    $date_duration_end = date("Y",strtotime($node->get('field_date_duration')->end_value));
    $character = $node->get('field_character')->value;
    $stream = Term::load($node->get('field_stream')->target_id)->get('name')->value;
    $roll = $node->get('field_class_roll_no')->value;

    $table_field = $node->get('field_subject_marks_details')->value;
    unset($table_field['caption']);

    if($stream == 'Arts') {
      $stream = 'I.A.';
    } else if($stream == 'Science') {
      $stream = 'I.Sc.';
    } else {
      $stream = 'I.Com.';
    }

    if($node->get('field_gender')->value == 'Female'){
      $his_her = 'her';
      $he_she = 'She';
      $son_daughter = 'daughter';
    }else{
      $his_her = 'his';
      $he_she = 'He';
      $son_daughter = 'son';
    }

    
    $image_text = 'Name='.$name.' Class = Intermediate Father\' Name='.$f_name.' Date Duration ='.$date_duration_start.' - '.$date_duration_end.' Certificate Number='.$tc_no;
    $google_text = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($image_text) . "&choe=UTF-8";


    $data = '
      <div class=" text-center">
        <a class="print btn btn-success"><i class="fa fa-print"></i> Print</a>
      </div>
      <div id="clc-wrapper">
        <table class="tc-header">
          <tr>
            <td width="50%"><b>Sl.No :</b> '.$tc_no.'</td>
            <td class="tc-date" width="50%"><b>Issue Date: </b><span class="dot">'.$issue_date.'</span></td>
          </tr>
        </table>
        
        <p class="tc-detail">
          This is to certify that <span class="dot">'.$name.'</span> '. $son_daughter .' of <span class="dot">'.$f_name.'</span> Mother\'s name
          <span class="dot">'.$m_name.'</span> residing at village <span class="dot">'.$vill.'</span> and subdivision <span class="dot">'.$locality.'</span> is/was a student
          has been the student of class <span class="dot">'. $stream .'</span> Roll No. <span class="dot">'. $roll .'</span> during the session <span class="dot">'.$date_duration_start.' - '.$date_duration_end.'</span>
          '. $he_she .' has paid all the fees and dues of the college.
        </p>

        <p class="tc-detail">
        While at the college '. $his_her .' conduct was <span class="dot">'. $character .'</span> and nothing against '. $his_her .' character 
        was reported.
        </p>

        <p class="tc-detail tc-subject">Details of Marks obtained in his/her Subject Examination are following:--</p>';

      $data .= '
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
        <table class="footer">
          <tr>
            <td class="row-1"></td>
            <td class="row-1" align="center"><img src="'.$google_text.'" width="100px" height="100px"/></td>
            <td class="row-1"></td>
          </tr>
          <tr>
            <td class="row-2" align="left"><b>Assistant</b></td>
            <td class="row-2" align="center"><b>H.A.</b></td>
            <td class="row-2" align="right"><b>Principal</b></td>
          </tr>
        </table>
      </div>
    ';

    return array(
      '#type' => 'markup',
      '#markup' => $data,
      '#attached' => [
        'library' => [
          'ug_clc/ug_clc',
        ],
      ],
    );

  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
    public function access(AccountInterface $account, $nid) { 
      // Check for the Certificate approval
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      if($node->get('field_approval')->value == "Approved"){
        return AccessResult::allowed();
      }
      return AccessResult::forbidden();
    }

}
