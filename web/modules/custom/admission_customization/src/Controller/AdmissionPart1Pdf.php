<?php

namespace Drupal\admission_customization\Controller;

use Drupal\Core\Url;
use Mpdf\HTMLParserMode;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdmissionPart1Pdf.
 */
class AdmissionPart1Pdf extends ControllerBase {

  /**
   * Returns content for this controller.
   */
  public function content($nid) {

    $node = Node::load($nid);
    $photo = File::load($node->get('field_photo')->target_id);
    $signature = File::load($node->get('field_student_signature')->target_id);
    $signature_hindi = File::load($node->get('field_student_signature_hindi')->target_id);
    $admit_card_10 = File::load($node->get('field_10th_admit_card')->target_id);
    $marksheet_10 = File::load($node->get('field_10th_marksheet')->target_id);
    $clc_10 = $migration_10 = $affidavit = $caste_certificate_url = $registration = $ofss = '';
    if (!empty($node->get('field_10th_slc')->target_id)) {
        $clc_10 = File::load($node->get('field_10th_slc')->target_id);
    }
    if (!empty($node->get('field_10th_registration')->target_id)) {
        $registration = File::load($node->get('field_10th_registration')->target_id);
    }
    if (!empty($node->get('field_migration_certificate')->target_id)) {
        $migration_10 = File::load($node->get('field_migration_certificate')->target_id);
    }
    if (!empty($node->get('field_ofss_form')->target_id)) {
        $ofss = File::load($node->get('field_ofss_form')->target_id);
    }
    if (!empty($node->get('field_affidavit_certificate')->target_id)) {
        $affidavit = File::load($node->get('field_affidavit_certificate')->target_id);
    }
    $site = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    $site = substr_replace($site, "", -1);
    if(!empty($node->get('field_caste_certificate')->target_id)){
      $caste_certificate = File::load($node->get('field_caste_certificate')->target_id);
      $caste_certificate_url = $site.$caste_certificate->createFileUrl();
      $caste_certificate_text = 'View Document';   
    }

    $optional = $additional = '';
    if(current($node->get('field_stream')->referencedEntities())->getName() == 'Arts'){
        foreach ($node->get('field_optional_arts')->referencedEntities() as $optional_arts) {
            $optional .= '<div>'.$optional_arts->getName().'</div>';
        }
        if (!empty($node->get('field_additional_arts')->target_id)) {
            $additional = current($node->get('field_additional_arts')->referencedEntities())->getName();
        }
        
    }
    if(current($node->get('field_stream')->referencedEntities())->getName() == 'Science'){
        foreach ($node->get('field_optional_science')->referencedEntities() as $optional_science) {
            $optional .= '<div>'.$optional_science->getName().'</div>';
        }
        if (!empty($node->get('field_additional_science')->target_id)) {
            $additional = current($node->get('field_additional_science')->referencedEntities())->getName();
        }
    }
    if(current($node->get('field_stream')->referencedEntities())->getName() == 'Commerce'){
        foreach ($node->get('field_optional_commerce')->referencedEntities() as $optional_commerce) {
            $optional .= '<div>'.$optional_commerce->getName().'</div>';
        }
        if (!empty($node->get('field_additional_commerce')->target_id)) {
            $additional = current($node->get('field_additional_commerce')->referencedEntities())->getName();
        }
    }

    $data = '
    <style>
     body, p, td, div { font-family: freesans; }
    </style>
       <p>नोट: नामांकन प्रपत्र भरकर सभी कागजातों सहित हार्ड कॉपी महाविद्यालय कार्यालय में जमा करें अन्यथा नामांकन मान्य नहीं समझा जायेगा</p>
       <h2>Admission Form</h2> 
       <table style="width: 100%">
            <tr>
                <td style="width: 70%">
                    <table style="width: 100%">
                        <tr>
                            <th style="width: 50%; text-align: left">Form Number</th>
                            <td style="width: 50%">'.$node->get('field_form_number')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Reference No</th>
                            <td style="width: 50%">'.$node->get('field_application_no')->value.'</td>
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
                            <th style="width: 50%; text-align: left">Caste Category</th>
                            <td style="width: 50%">'.$node->get('field_caste_category')->value.'</td>
                        </tr>
                        <tr>
                            <th style="width: 50%; text-align: left">Selection Category</th>
                            <td style="width: 50%">'.$node->get('field_selection_category')->value.'</td>
                        </tr>
                        <tr>
                          <th style="width: 50%; text-align: left">Date of Submission</th>
                          <td style="width: 50%">'.date('d/m/Y', $node->get('created')->value).'</td>
                        </tr>';
                        if(!empty($node->get('field_class_roll_no')->value)){
                          $data .= '<tr>
                            <th style="width: 50%; text-align: left">Class Roll number</th>
                            <td style="width: 50%">'.$node->get('field_class_roll_no')->value.'</td>
                          </tr>';
                        }
                    $data .= '</table>
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
        <h3>Matriculation/10th details</h3>
        <table  style="width: 100%">
            <tr>
                <th style="width: 25%; border:1px solid black">Name Of School/College</th>
                <th style="width: 25%; border:1px solid black">Board/University</th>
                <th style="width: 25%; border:1px solid black">Percentage Of Marks</th>
                <th style="width: 25%; border:1px solid black">Year of Passing</th>
            </tr>
            <tr>';
                $data .= '<td style="width: 25%; border:1px solid black; text-align:center">'.$node->get('field_name_of_college')->value.'</td>
                <td style="width: 25%; border:1px solid black; text-align:center">'.current($node->get('field_board_university')->referencedEntities())->getName().'</td>
                <td style="width: 25%; border:1px solid black; text-align:center">'.$node->get('field_percentage_of_marks')->value.'</td>
                <td style="width: 25%; border:1px solid black; text-align:center">'.$node->get('field_year_of_passing')->value.'</td>
            </tr>
        </table>
        <h3>Subject Details</h3>
        <table  style="width: 100%">
            <tr>
                <th style="width: 25%; border:1px solid black">Compulsory Group 1</th>
                <th style="width: 25%; border:1px solid black">Compulsory Group 2</th>
                <th style="width: 25%; border:1px solid black">Optional</th>
                <th style="width: 25%; border:1px solid black">Additional</th>';
            $data .= '</tr>
            <tr>
                <td style="width: 25%; border:1px solid black; text-align:center">'.current($node->get('field_compulsory')->referencedEntities())->getName().'</td>
                <td style="width: 25%; border:1px solid black; text-align:center">'.current($node->get('field_compulsory_2')->referencedEntities())->getName().'</td>
                <td style="width: 25%; border:1px solid black; text-align:center">'.$optional.'</td>
                <td style="width: 25%; border:1px solid black; text-align:center">'.$additional.'</td>';
            $data .= '</tr>
        </table>
        <h3>Documents</h3>
        <table  style="width: 100%">
            <tr>
                <th style="width: 20%; border:1px solid black">10th Admit card</th>
                <th style="width: 20%; border:1px solid black">10th Marksheet</th>';
                if(!empty($clc_10)){
                    $data .= '<th style="width: 20%; border:1px solid black">10th School Leaving Certificate(SLC)</th>';
                }
                if(!empty($registration)){
                    $data .= '<th style="width: 20%; border:1px solid black">Registration</th>';
                }
                if(!empty($migration_10)){
                    $data .= '<th style="width: 20%; border:1px solid black">Migration Certificate</th>';
                }
                if(!empty($ofss)){
                    $data .= '<th style="width: 20%; border:1px solid black">OFSS Intimation Letter</th>';
                }
                if(!empty($caste_certificate_url)){
                  $data .= '<th style="width: 20%; border:1px solid black">Caste Certificate</th>';
                }
                if(!empty($affidavit)){
                    $data .= '<th style="width: 20%; border:1px solid black">Affidavit certificate</th>';
                }
            $data .= '</tr>
            <tr>
                <td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$admit_card_10->createFileUrl().'">View Document</a></td>
                <td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$marksheet_10->createFileUrl().'">View Document</a></td>';
                if(!empty($clc_10)){
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$clc_10->createFileUrl().'">View Document</a></td>';
                }
                if(!empty($registration)){
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$registration->createFileUrl().'">View Document</a></td>';
                }
                if(!empty($migration_10)){
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$migration_10->createFileUrl().'">View Document</a></td>';
                }
                if(!empty($ofss)){
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$ofss->createFileUrl().'">View Document</a></td>';
                }
                if(!empty($caste_certificate_url)){
                  $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$caste_certificate_url.'">'.$caste_certificate_text.'</a></td>';
                }
                if(!empty($affidavit)){
                    $data .= '<td style="width: 20%; border:1px solid black; text-align:center"><a href="'.$site.$affidavit->createFileUrl().'">View Document</a></td>';
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
            
            $data .= '<h3 style="width: 100%; border-top:1px dashed black; text-align:center; margin-top:10px; padding-top:10px;">Receipt</h3>
            <table  style="width: 100%">
                <tr>
                    <th>Name</th>
                    <th>Stream</th>
                    <th>Form Number</th>
                    <th>Reference No</th>
                    <th></th>
                </tr>
                <tr>
                    <td style="width: 20%; text-align:center">'.$node->getTitle().'</td>
                    <td style="width: 20%; text-align:center">'.current($node->get('field_stream')->referencedEntities())->getName().'</td>
                    <td style="width: 20%; text-align:center">'.$node->get('field_form_number')->value.'</td>
                    <td style="width: 20%; text-align:center">'.$node->get('field_application_no')->value.'</td>
                    <td style="width: 20%; text-align:center">Signature</td>
                </tr>
            </table>';
        }
        
        
    global $base_url;
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $mpdf->SetHeader('|<img src="'.$base_url.'/themes/custom/allinonecms/images/allinonecms_pdf_logo.png" />|');
    $mpdf->AddPageByArray([
        'margin-top' => '35',
        'margin-bottom' => '5',
    ]);
    $stylesheet = file_get_contents('modules/custom/admission_customization/css/pdf.css');
    $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($data);
    $mpdf->Output($node->getTitle().'.pdf', 'D');
    return new Response(
        'pdf generated success', 
         Response::HTTP_OK
    );
  }
}
