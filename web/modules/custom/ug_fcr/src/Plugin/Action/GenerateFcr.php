<?php

namespace Drupal\ug_fcr\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ug_fcr\UgFcrContent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Action description.
 *
 * @Action(
 *   id = "generate_fcr",
 *   label = @Translation("Generate FCR"),
 *   type = ""
 * )
 */
class GenerateFcr extends ViewsBulkOperationsActionBase {

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
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()
      ->setCreator('ITBox')
      ->setLastModifiedBy('ITBox')
      ->setTitle("UG FCR")
      ->setLastModifiedBy('ITBox')
      ->setDescription('Show UG Fcr data')
      ->setSubject('Show UG Fcr data')
      ->setKeywords('Show UG Fcr data')
      ->setCategory('programming');

    $styleArrayHead = array(
      'font' => array(
        'bold' => true,
        'color' => array(
          'rgb' => 'FF5733'
        ),
      )
    );

    // Get the active sheet.
    $spreadsheet->setActiveSheetIndex(0);
    $worksheet = $spreadsheet->getActiveSheet();
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="fcr_report.xlsx"');
    header('Cache-Control: max-age=0');

    // $worksheet ->getCell('A1')->setValue('Receipt Details');
    // $worksheet->mergeCells('A1:B1');

    // $worksheet ->getCell('C1')->setValue('B.U. Account');
    // $worksheet->mergeCells('C1:F1');
    // $worksheet ->getCell('G1')->setValue('General Account');
    // $worksheet->mergeCells('G1:AF1');
    // $worksheet ->getCell('AG1')->setValue('Examination Account');
    // $worksheet->mergeCells('AG1:AH1');

   // $worksheet->getCell('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    //$worksheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

    $asci_start = 65;

    $i = 1;
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Date of payment');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Name');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Roll No.');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Tuition Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Admission Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Test Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('College Dev. fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Magazine Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Poor Boys Fund');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('College Exam. fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Electricity Charge');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Common Room Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Games Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Library Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('College Societies');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Medical Exam Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Lib. Money');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Cycle levy');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Identity card fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Lib. I. Card');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Excursion Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Gradening charge');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Furniture Fee');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Extra Curricular');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('N.C.C.');
    $worksheet ->getCell(chr($asci_start++).$i)->setValue('Red Cross');
    if($asci_start > 90){
      $asci_start = $asci_end = 65;
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('Generator Fee');
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('Necessary Fee');
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('Sainik Fee');
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('Progress Fee');
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('N.S.S. Fee');
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('Cost of Remittance');
      $worksheet ->getCell(chr($asci_start).chr($asci_end++).$i)->setValue('Total Sum');
    }
    $total_row = sizeof($entities);
    foreach ($entities as $delta => $entity) {
      $related_fee_vid = $entity->get('field_related_fee')->target_id;
      $fcr_services = \Drupal::service('ug_fcr.settings');
      $fcr_nid = $fcr_services->get_frc_nid_by_product_variation_id($related_fee_vid);
      if(!empty($fcr_nid)) {
        $node_obj = \Drupal::entityTypeManager()->getStorage('node')->load(current($fcr_nid));
      }else{
        $node_obj = [];
      }
      if(!empty($node_obj)){
        $i ++;
        $asci_start = 65;
        $worksheet ->getCell(chr($asci_start++).$i)->setValue(date('d/m/Y',strtotime($entity->get('field_payment_date')->value)));
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($entity->getTitle());
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($entity->get('field_class_roll_no')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_tuition_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_admission_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_test_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_college_development_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_magazine_fund')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_poor_boys_fund')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_internal_exam_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_electricity_fee_generator')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_common_room_fund')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_games_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_library')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_college_societies')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_medical_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_lib_money')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_cycle_levy')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_identity_card_fee')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_lib_i_card')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_excursion_charge')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_gradening_charge')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_furniture_charge')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_extra_curricular_fee_cultu')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_n_c_c_')->value);
        $worksheet ->getCell(chr($asci_start++).$i)->setValue($node_obj->get('field_army_flag_red_cross_t_b')->value);
        if($asci_start > 90) {
            $asci_start2 = $asci_end = 65;
            $worksheet ->getCell(chr($asci_start2).chr($asci_end++).$i)->setValue($node_obj->get('field_generator_fee')->value);
            $worksheet ->getCell(chr($asci_start2).chr($asci_end++).$i)->setValue($node_obj->get('field_necessary_fee')->value);
            $worksheet ->getCell(chr($asci_start2).chr($asci_end++).$i)->setValue($node_obj->get('field_sainik_fee')->value);
            $worksheet ->getCell(chr($asci_start2).chr($asci_end++).$i)->setValue($node_obj->get('field_progress_fee')->value);
            $worksheet ->getCell(chr($asci_start2).chr($asci_end++).$i)->setValue($node_obj->get('field_nss_fee')->value);
            $worksheet ->getCell(chr($asci_start2).chr($asci_end++).$i)->setValue($node_obj->get('field_cost_of_remittance_1x3')->value);
            $worksheet->setCellValue(chr($asci_start2).chr($asci_end++).$i, '=SUM(D'.$i.':AG'.$i.')');
        }
      }
    }
    $i ++;
    $worksheet->getStyle('AG1:AG'.$i)->applyFromArray($styleArrayHead);
    $worksheet->getStyle('C'.$i)->applyFromArray($styleArrayHead);

    $sheet = ['D','E','F','G','H','I','J','K','L','M','N','O','P',
      'Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG'];
    $worksheet->setCellValue('C'.$i, 'Total');
    foreach($sheet as $row){
      $worksheet->setCellValue($row.$i, '=SUM('.$row.'2:'.$row.$i.')');
      $worksheet->getStyle($row.$i)->applyFromArray($styleArrayHead);
    }

    $writer->save('sites/default/files/fcr_report.xlsx');
    \Drupal::messenger()->addMessage(t('FCR file will be downloaded in <b>5 seconds</b> if not then click <a data-auto-download href="@url"><b>here</b></a> to download.', ['@url' => 'sites/default/files/fcr_report.xlsx']));
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('view', $account, TRUE);
    return $return_as_object ? $result : $result->isAllowed();
  }

}