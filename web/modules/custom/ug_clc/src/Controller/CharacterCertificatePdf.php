<?php

namespace Drupal\ug_clc\Controller;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CharacterCertificatePdf.
 */
class CharacterCertificatePdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {

    $node = Node::load($nid);
    $issue_date = $node->get('field_date_of_issue')->value;
    $issue_date = date("d/m/Y",strtotime($issue_date));
    $cc_no = $node->get('field_character_certificate_no')->value;
    $session = Term::load($node->get('field_session')->target_id)->get('name')->value;
    $stream = Term::load($node->get('field_stream')->target_id)->get('name')->value;

    $name = $node->getTitle();
    $roll = $node->get('field_class_roll_no')->value;
    $f_name = $node->get('field_father_s_name')->value;
    $m_name = $node->get('field_mother_s_name')->value;
    $dob = date('d/m/Y',strtotime(date($node->get('field_date_of_birth')->value.'12:00:00')));
    $mobile = $node->get('field_mobile')->value;

    if($node->get('field_gender')->value == 'Female'){
      $him_her = 'her';
      $his_her= 'Her';
      $he_she = 'She';
      $son_daughter = 'daughter';
    }else{
      $him_her = 'him';
      $his_her = 'His';
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
        <table class="cc-header">
          <tr>
            <td width="50%"><b>Sl.No :</b> '.$cc_no.'</td>
            <td class="clc-date" width="50%"><b>Issue Date: </b><span class="dot">'.$issue_date.'</span></td>
          </tr>
        </table>
        <p class="form-detail cc">
          This is to certify that <span class="dot">'.$name.'</span> '. $son_daughter .' of <span class="dot">'.$f_name.'</span> Mother\'s name 
          <span class="dot father"> '.$m_name.' </span> is/was a student of this college in <span class="dot">Intermediate</span> classes during session
          <span class="dot">'.$session.'</span>
        </p>
        <p class="cc-remark">
          <b>'. $he_she .' was very Sincere and Punctual in acedemics activities in the college. '. $his_her .' character and 
          discipline at this college was satisfactory. I wish '. $him_her .' a bright future in life. </b>
        </p>
        
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
