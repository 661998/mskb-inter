<?php

namespace Drupal\ug_clc\Controller;

use Drupal\Core\Url;
use Mpdf\HTMLParserMode;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClcReceiptPdf.
 */
class ClcReceiptPdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {
    $node = Node::load($nid);

    $examination_date = date("d-M-Y",strtotime($node->get('field_examination_date')->value));
    $data = '
      <table width="100%">
        <tr>
          <td colspan="4" align="center" style="padding-bottom:30px;"><h2>CLC Apply Receipt</h2></td>
        </tr>
        <tr>
          <td colspan="2"><h3>Personal Details</h3></td>
          <th colspan="2" align="right">Date of Submission: '.date('d/m/Y', $node->get('created')->value).'</th>
        </tr>
        <tr>
          <td width="25%"><b>Name of Student: </b></td>
          <td width="25%">'.$node->getTitle().'</td>
          <td width="25%"><b>Father\'s Name: </b></td>
          <td width="25%">'.$node->get('field_father_s_name')->value.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Mobile: </b></td>
          <td width="25%">'.$node->get('field_mobile')->value.'</td>
          <td width="25%"><b>Aadhar Number: </b></td>
          <td width="25%">'.$node->get('field_aadhar_number')->value.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Class Roll Number: </b></td>
          <td width="25%">'.$node->get('field_class_roll_no')->value.'</td>
          <td width="25%"><b>Session: </b></td>
          <td width="25%">'.current($node->get('field_session')->referencedEntities())->getName().'</td>
        </tr>
        <tr>
          <td width="25%"><b>Stream: </b></td>
          <td width="25%">'.current($node->get('field_stream')->referencedEntities())->getName().'</td>
          <td width="25%"><b>Date of Birth: </b></td>
          <td width="25%">'.date('d/m/Y',strtotime(date($node->get('field_date_of_birth')->value.'12:00:00'))).'</td>
        </tr>
        <tr>
          <td width="25%"><b>Gender: </b></td>
          <td width="25%">'.$node->get('field_gender')->value.'</td>
          <td width="25%"><b>Permanent Address: </b></td>
          <td width="25%">'.$node->get('field_permanent_address')->getValue()[0]['address_line1'].', '.
          $node->get('field_permanent_address')->getValue()[0]['locality'].', '.
          $node->get('field_permanent_address')->getValue()[0]['administrative_area'].', '.
          $node->get('field_permanent_address')->getValue()[0]['postal_code'].'</td>
        </tr>

        <tr>
          <td colspan="4" class="pt-3"><h3>Examination Details</h3></td>
        </tr>
        <tr>
          <td width="25%"><b>Passing Division: </b></td>
          <td width="25%">'.current($node->get('field_passing_division')->referencedEntities())->getName().'</td>
          <td width="25%"><b>Exam Year: </b></td>
          <td width="25%">'.$node->get('field_exam_year')->value.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Examination Roll Number: </b></td>
          <td width="25%">'.$node->get('field_roll_number')->value.'</td>
          <td width="25%"><b>Registration Number: </b></td>
          <td width="25%">'.$node->get('field_registration_number')->value.'</td>
        </tr>
        <tr>
          <td width="25%"><b>Examination Date:</b></td>
          <td width="25%">'.$examination_date.'</td>
        </tr>
      </table>
    ';

    $data .= '
    <h3>Payment Details</h3>
      <table width="100%">
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
    $data .='  
      <table width="100%">
        <tr>
          <td class="py-5 sign"></td>
          <td class="sign-box"><strong>Signature of Student with date</strong></td>
        </tr>
        <tr>
          <td colspan="2" class="py-1"><strong>1. A.</strong> Clearing Certificate for the Following.</td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">Office(Accounts)</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">Library</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">Athletic</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">Common Room</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">Examination</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">Admission</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">NSS</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">NCC</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="py-1"><p><strong>1. B.</strong> For Science Student only:-</p></td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="py-1"><p><strong>1. C.</strong> For Arts Only:-</p></td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td colspan="2" class="py-1"><strong>2.</strong> Office note regarding fulfillment of all the conditions:</td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">i. M.R. No. with date &amp; signature of cashier</td>
          <td class="border-bottom-dotted"></td>
        </tr>
        <tr>
          <td width="50%" class="ps-3 py-1">ii. CLC/TC No. with date.</td>
          <td class="border-bottom-dotted"></td>
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
