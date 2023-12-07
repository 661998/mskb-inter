<?php

namespace Drupal\examination_customization\Controller;

use Drupal\Core\Url;
use Mpdf\HTMLParserMode;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExaminationPart1Pdf.
 */
class ExaminationPart1Pdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {

    $node = Node::load($nid);
    $photo = File::load($node->get('field_photo')->target_id);
    $signature = File::load($node->get('field_student_signature')->target_id);
    $signature_hindi = File::load($node->get('field_student_signature_hindi')->target_id);
    $admit_card_12 = File::load($node->get('field_intermediate_admit_card')->target_id);
    $marksheet_12 = File::load($node->get('field_intermediate_marksheet')->target_id);
    $university_e_form = File::load($node->get('field_examination_form')->target_id);
    $grad_reg_card = File::load($node->get('field_registration_card')->target_id);
    $part1_promoted_admit = $part1_promoted_marks = $caste_certificate_url = '';
    if (!empty($node->get('field_part_1_admit_card')->target_id)) {
        $part1_promoted_admit = File::load($node->get('field_part_1_admit_card')->target_id);
    }
    if (!empty($node->get('field_part_1_marksheet')->target_id)) {
        $part1_promoted_marks = File::load($node->get('field_part_1_marksheet')->target_id);
    }
    $site = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    $site = substr_replace($site, "", -1);
    if(!empty($node->get('field_caste_certificate')->target_id)){
        $caste_certificate = File::load($node->get('field_caste_certificate')->target_id);
        $caste_certificate_url = $site.$caste_certificate->createFileUrl();
        $caste_certificate_text = 'View Document';   
    }
    
    $data = '
     <style>
       body, p, td, div { font-family: freesans; }
     </style>
      <p> नोट: परीक्षा प्रपत्र भरकर सभी कागजातों सहित हार्ड कॉपी महाविद्यालय कार्यालय में जमा करें अन्यथा परीक्षा प्रपत्र मान्य नहीं समझा जायेगा.</p>
      <h2>Part 1 Examination Form</h2>
        <table style="width: 100%">
            <tr>
                <td style="width: 70%">
                    <table style="width: 100%">
                        <tr>
                            <th style="width: 50%; text-align: left">Student Type</th>
                            <td style="width: 50%">'.$node->get('field_student_type')->value.'</td>
                        </tr>
                        <tr>
                          <th style="width: 50%; text-align: left">Form Number</th>
                          <td style="width: 50%">'.$node->get('field_form_number')->value.'</td>
                        </tr>
                        <tr>
                          <th style="width: 50%; text-align: left">Application No</th>
                          <td style="width: 50%">'.$node->get('field_application_no')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Registration Number</th>
                            <td style="width: 50%">'.$node->get('field_registration_number')->value.'</td>
                        </tr>
                        <tr>
                          <th style="width: 50%; text-align: left">Registration Year</th>
                          <td style="width: 50%">'.$node->get('field_registration_year')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">University Exam Form Number</th>
                            <td style="width: 50%">'.$node->get('field_examination_form_number')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Class Roll Number</th>
                            <td style="width: 50%">'.$node->get('field_class_roll_number')->value.'</td>
                        </tr>
                        <tr>
                          <th style="width: 50%; text-align: left">Session</th>
                          <td style="width: 50%">'.current($node->get('field_session')->referencedEntities())->getName().'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Stream</th>
                            <td style="width: 50%">'.current($node->get('field_stream')->referencedEntities())->getName().'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Course</th>
                            <td style="width: 50%">'.current($node->get('field_course')->referencedEntities())->getName().'</td>
                        </tr>
                        <tr>
                          <th style="width: 50%; text-align: left">Date of Submission</th>
                          <td style="width: 50%">'.date('d/m/Y', $node->get('created')->value).'</td>
                        </tr>
                    </table>
                    <h3>Personal Details</h3>
                    <table style="width: 100%">
                        <tr>
                          <th style="width: 50%; text-align: left">Name of Student</th>
                          <td style="width: 50%">'.$node->getTitle().'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Father\'s Name</th>
                            <td style="width: 50%">'.$node->get('field_father_s_name')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Mother\'s Name</th>
                            <td style="width: 50%">'.$node->get('field_mother_s_name')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Date of Birth</th>
                            <td style="width: 50%">'. date('d/m/Y',strtotime(date($node->get('field_date_of_birth')->value.'12:00:00'))) .'</td>
                        </tr>
                        <tr>
                                <th style="width: 50%; text-align: left">Aadhar Number</th>
                                <td style="width: 50%">'.$node->get('field_aadhar_number')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Religion</th>
                            <td style="width: 50%">'.current($node->get('field_religion')->referencedEntities())->getName().'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Gender</th>
                            <td style="width: 50%">'.$node->get('field_gender')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Caste Category</th>
                            <td style="width: 50%">'.$node->get('field_caste_category')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Differently Abled</th>
                            <td style="width: 50%">'.$node->get('field_pwd')->value.'</td>
                        </tr>';
                        if($node->get('field_pwd')->value == 'YES'){
                            $data .= '<tr>
                                <th style="width: 50%; text-align: left">Type Of Disability</th>
                                <td style="width: 50%">'.$node->get('field_type_of_disability')->value.'</td>
                            </tr>';
                        }
                        $data .= '<tr>
                            <th style="width: 50%; text-align: left">Nationality</th>
                            <td style="width: 50%">'.$node->get('field_nationality')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Mobile</th>
                            <td style="width: 50%">'.$node->get('field_mobile')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Email Id</th>
                            <td style="width: 50%">'.$node->get('field_email_id')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Permanent Address</th>
                            <td style="width: 50%">'.
                              $node->get('field_permanent_address')->getValue()[0]['address_line1'].', '.
                              $node->get('field_permanent_address')->getValue()[0]['locality'].', '.
                              $node->get('field_permanent_address')->getValue()[0]['administrative_area'].', '.
                              $node->get('field_permanent_address')->getValue()[0]['postal_code']
                            .'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Present Address</th>
                            <td style="width: 50%">'.
                            $node->get('field_correspondence_address')->getValue()[0]['address_line1'].', '.
                            $node->get('field_correspondence_address')->getValue()[0]['locality'].', '.
                            $node->get('field_correspondence_address')->getValue()[0]['administrative_area'].', '.
                            $node->get('field_correspondence_address')->getValue()[0]['postal_code']
                            .'</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%">
                  <table style="width: 100%">
                    <tr>
                      <td><img src="'.$photo->createFileUrl().'" width="80px" height="100px" style="align-itme:right"/></td>
                    </tr>
                    <tr>
                      <td><img src="'.$signature->createFileUrl().'" width="80px" height="30px" style="align-itme:right"/></td>
                    </tr>
                    <tr>
                      <td><img src="'.$signature_hindi->createFileUrl().'" width="80px" height="30px" style="align-itme:right"/></td>
                    </tr>
                  </table>
                </td>
            </tr>
        </table>
        <h3>Subject Details</h3>
        <table  style="width: 100%">
          <tr>
            <th style="width: 20%; border:1px solid black">Honours Subject</th>
            <th style="width: 20%; border:1px solid black">Subsidiary 1</th>
            <th style="width: 20%; border:1px solid black">Subsidiary 2</th>
            <th style="width: 25%; border:1px solid black">Language Composition</th>
          </tr>
        </table>
        <h3>Documents</h3>
        <table  style="width: 100%">
            <tr>
                <th style="width: 20%; border:1px solid black">Intermediate Admit card</th>
                <th style="width: 20%; border:1px solid black">Intermediate Marksheet</th>
                <th style="width: 20%; border:1px solid black">University Examination Form</th>
                <th style="width: 20%; border:1px solid black">Graduation Registration Card</th>';
                if(!empty($caste_certificate_url)){
                    $data .= '<th style="width: 20%; border:1px solid black">Caste Certificate</th>';
                }
                if(!empty($part1_promoted_admit)){
                    $data .= '<th style="width: 20%; border:1px solid black">Part 1 Promoted Admit Card</th>';
                }
                if(!empty($part1_promoted_marks)){
                    $data .= '<th style="width: 20%; border:1px solid black">Part 1 Promoted Marksheet</th>';
                }
            $data .= '</tr>
            <tr>
                <td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$admit_card_12->createFileUrl().'">View Document</a></td>
                <td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$marksheet_12->createFileUrl().'">View Document</a></td>
                <td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$university_e_form->createFileUrl().'">View Document</a></td>
                <td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$grad_reg_card->createFileUrl().'">View Document</a></td>';
                if(!empty($caste_certificate_url)){
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$caste_certificate_url.'">'.$caste_certificate_text.'</a></td>';
                }
                if (!empty($part1_promoted_admit)) {
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$part1_promoted_admit->createFileUrl().'">View Document</a></td>';
                }
                if (!empty($part1_promoted_marks)) {
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$part1_promoted_marks->createFileUrl().'">View Document</a></td>';
                }
            $data .= '</tr>
         </table>
         <h3>Payment Details</h3>
         <table  style="width: 100%">
         <tr>';
             if(!empty($node->get('field_utr_ref_number')->value)){
                 $data .= '<th style="width: 20%; border:1px solid black">UTR/Ref. Number</th>';
             }
             if(!empty($node->get('field_payment_amount')->value)){
                 $data .= '<th style="width: 20%; border:1px solid black">Amount</th>';
             }
             if(!empty($node->get('field_payment_date')->value)){
                 $data .= '<th style="width: 20%; border:1px solid black">Payment Date</th>';
             }
             if(!empty($node->get('field_utr_ref_number_2')->value)){
                 $data .= '<th style="width: 20%; border:1px solid black">UTR/Ref. Number2</th>';
             }
             if(!empty($node->get('field_payment_date_2')->value)){
                 $data .= '<th style="width: 20%; border:1px solid black"></th>';
             }
         $data .= '</tr>
         <tr>';
             if(!empty($node->get('field_utr_ref_number')->value)){
               $data .= '<td style="width: 20%; border:1px solid black; text-align:center">'.$node->get('field_utr_ref_number')->value.'</td>';
             }
             if(!empty($node->get('field_payment_amount')->value)){
                 $data .= '<td style="width: 20%; border:1px solid black; text-align:center">'.$node->get('field_payment_amount')->value.'</td>';
             }
             if(!empty($node->get('field_payment_date')->value)){
                 $data .= '<td style="width: 20%; border:1px solid black; text-align:center">'.date('d/m/Y',strtotime($node->get('field_payment_date')->value)).'</td>';
             }
             if(!empty($node->get('field_utr_ref_number_2')->value)){
                 $data .= '<td style="width: 20%; border:1px solid black; text-align:center">'.$node->get('field_utr_ref_number_2')->value.'</td>';
             }
             if(!empty($node->get('field_payment_date_2')->value)){
                 $data .= '<td style="width: 20%; border:1px solid black; text-align:center">'.date('d/m/Y',strtotime($node->get('field_payment_date_2')->value)).'</td>';
             }
         $data .= '</tr>
     </table>';
     if(!empty($node->get('field_payment_received')->value == 1)){
         $data .= '<h4>Your Payment has been Received.</h4>';
     }
    global $base_url;
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $mpdf->SetHeader('|<img src="'.$base_url.'/themes/custom/allinonecms/images/allinonecms_pdf_logo.png" />|');
    $mpdf->AddPageByArray([
      'margin-top' => '37',
      'margin-bottom' => '15',
    ]);
    $stylesheet = file_get_contents('modules/custom/examination_customization/css/pdf.css');
    $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($data);
    $mpdf->Output($node->getTitle().'.pdf', 'D');
    return new Response(
        'pdf generated success', 
         Response::HTTP_OK
    );
  }
}
