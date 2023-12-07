<?php

namespace Drupal\ug_clc\Controller;

use Drupal\node\Entity\Node;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ClcPdf.
 */
class ClcPdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {

    $node = Node::load($nid);
    $exam_year = $node->get('field_exam_year')->value;
    $exam_date = date("F",strtotime($node->get('field_examination_date')->value));
    $issue_date = $node->get('field_date_of_issue')->value;
    $issue_date = date("d/m/Y",strtotime($issue_date));
    $clc_no = $node->get('field_certificate_number')->value;

    $name = $node->getTitle();
    $roll = $node->get('field_class_roll_no')->value;
    $f_name = $node->get('field_father_s_name')->value;
    $m_name = $node->get('field_mother_s_name')->value;
    $dob = date('d/m/Y',strtotime(date($node->get('field_date_of_birth')->value.'12:00:00')));
    $mobile = $node->get('field_mobile')->value;
    $stream = Term::load($node->get('field_stream')->target_id)->get('name')->value;
    $session = Term::load($node->get('field_session')->target_id)->get('name')->value;
    $passing_division = Term::load($node->get('field_passing_division')->target_id)->get('name')->value;
    $exam_roll = $node->get('field_roll_number')->value;
    $reg_no = $node->get('field_registration_number')->value;

    if($stream == 'Arts') {
      $stream = 'I.A.';
    } else if($stream == 'Science') {
      $stream = 'I.Sc.';
    } else {
      $stream = 'I.Com.';
    }

    if($node->get('field_gender')->value == 'Female'){
      $his_her = 'her';
      $his_Her = 'Her';
      $he_she = 'She';
      $son_daughter = 'daughter';
    }else{
      $his_her = 'his';
      $his_Her = 'His';
      $he_she = 'He';
      $son_daughter = 'son';
    }

    $image_text = 'Name='.$name.' Class Roll No='.$roll.' Father Name='.$f_name.' D.O.B='.$dob.' Mobile No.='.$mobile.' Stream='.$stream;
    $google_text = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($image_text) . "&choe=UTF-8";

    $data = '
    <div class=" text-center">
      <a class="print btn btn-success"><i class="fa fa-print"></i> Print</a>
    </div>
    <div id="clc-wrapper">
      <table class="clc-header">
        <tr>
          <td width="50%"><b>Sl.No :</b> '.$clc_no.'</td>
          <td class="clc-date" width="50%"><b>Issue Date: </b><span class="dot">'.$issue_date.'</span></td>
        </tr>
      </table>

      <p class="form-detail">This is to certify that <span class="dot name"> '.$name.'</span> 
      '. $son_daughter .' of <span class="dot father"> '.$f_name.' </span>Mother\'s 
      name <span class="dot father"> '.$m_name.' </span> has been a student 
      of this college during the session <span class="dot session"> '.$session.' </span>He passed the 
      <span class="dot exam">'.$stream.' </span> Examination held in the month of <span class="dot held"> '.$exam_date.' </span>
      Year <span class="dot year"> '.$exam_year.'</span> and was placed in the <span class="dot division"> '.$passing_division.' </span>
      Class/Division. '. $his_Her .' University/Board Roll No. was 
      <span class="dot exm-roll">'.$exam_roll.' </span> and Registration No. <span class="dot reg-no"> '.$reg_no.' </span>
      </p>

      <p class="clabel note"><b>Note: </b>While at the college '.$his_her.' conduct was good and nothing against '.$his_her.' character was reported </p>

      <p class="remark"><b>Remarks</b><span class="dot father"> '.$node->get('field_remark')->value.' </span></p>
      
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
        // Check permissions and combine that with any custom access checking needed. Pass forward
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
        if($node->get('field_approval')->value == "Approved"){
          return AccessResult::allowed();
        }
        return AccessResult::forbidden();
    }

}
