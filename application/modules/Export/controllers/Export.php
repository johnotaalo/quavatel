<?php
ini_set("memory_limit","512M");
class Export extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function export_report($data, $export_type, $title = NULL)
	{
		switch($export_type)
		{
			case 'pdf':
				
				$pdfFilePath = "{$data['title']}.pdf";
				
				if(file_exists($pdfFilePath) == FALSE)
				{
					$html = $this->load->view($data['view'], $data, true);
					$this->load->library("Pdf");
					$pdf = $this->pdf->load();
					$pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure <img class="emoji" draggable="false" alt="😉" src="https://s.w.org/images/core/emoji/72x72/1f609.png">
					
					if($data["orientation"] == "landscape"){$pdf->AddPage("L");}
					if(isset($data["css"]))
					{
						$stylesheet = file_get_contents('assets/css/custom_pdf.css');
					}else{
						$stylesheet = file_get_contents('assets/css/pdf.css');
					}
					$pdf->WriteHTML($stylesheet,1);
					$pdf->WriteHTML($html, 2); // write the HTML into the PDF
					$pdf->Output($pdfFilePath, 'D'); // save to file because we can
				}
				break;
			case 'excel':
				//load our new PHPExcel library
				$this->load->library('excel');
				//activate worksheet number 1
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->setCellValue('A1', $title);
				$letters = range('A', 'Z');
				$letter = "A";
				$count = count($data[0]);
				//echo $count;die;
				//$count = 676;
				$dividend = floor($count/ 26);
				$remainder = $count % 26;
				if($dividend < 1)
				{
					$letter = $letters[$count - 1];
				}
				else if($dividend >= 1 && $remainder > 0)
				{
					$letter = $letters[$dividend - 1] . $letters[$remainder - 1];
				}
				else if($dividend == 1 && $remainder == 0)
				{
					$letter = $letters[$count-1];
				}
				else if($dividend > 1 && $remainder == 0)
				{
					$letter = $letters[$dividend-2]. $letters[25];
				}
				//echo "<pre>";print_r($data);die;
				$all_data = count($data) + 3;
				$data_elements = count($data) - 1 + 2;
				//echo $data_elements;die;
				$this->excel->getActiveSheet()->getStyle("A3:F{$data_elements}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
				$this->excel->getActiveSheet()->fromArray($data, null, 'A2');
				$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle("A2:{$letter}2")->getFont()->setBold(true);
				$this->excel->getActiveSheet()->mergeCells("A1:{$letter}1");
				foreach($this->excel->getActiveSheet()->getRowDimensions() as $rd) { $rd->setRowHeight(-1); }
				$column = (in_array('SUPERVISOR', $data[0])) ? 'H' : 'F';
				$column2 = (in_array('DEDUCTIONS', $data[0])) ? 'G' : 'F';
				foreach(range('A',$column2) as $columnID) {
				    $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
				}
				$lastColumn = $this->excel->getActiveSheet()->getHighestColumn();
				//echo $lastColumn;die;
				//echo $all_data;die;
				$column = (in_array('SUPERVISOR', $data[0])) ? 'H' : 'G';
				for($column; $column <= $lastColumn; $column++)
				{
					//echo $column . " ";
					$this->excel->getActiveSheet()->SetCellValue("{$column}{$all_data}","=SUM({$column}3:{$column}{$data_elements})" );
				}
				$this->excel->getActiveSheet()->getStyle("{$column}3:{$lastColumn}{$all_data}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$filename= str_replace(" ", "_", $title) . '.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
				             
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
				break;
		}
	}
	function export_pdf($data, $project_type)
	{
		// As PDF creation takes a bit of memory, we're saving the created file in /downloads/reports/
		$filename = date('dFyhis');
		$pdfFilePath = ($project_type != "mss") ? "PDFReport-" . str_replace(' ', '_',$data['project_responses']['project_name']). "$filename.pdf" : "PDFReport-" . str_replace(' ', '_',$data['project_responses']->project_name). "$filename.pdf";
		
		if (file_exists($pdfFilePath) == FALSE)
		{
			ini_set('memory_limit','32M'); // boost the memory limit if it's low <img class="emoji" draggable="false" alt="😉" src="https://s.w.org/images/core/emoji/72x72/1f609.png">
			$html = $this->load->view($data['view'], $data, true); // render the view into HTML
			
			$this->load->library('Pdf');
			$pdf = $this->pdf->load();
			$pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure <img class="emoji" draggable="false" alt="😉" src="https://s.w.org/images/core/emoji/72x72/1f609.png">
			$stylesheet = file_get_contents('assets/css/pdf.css');
			$pdf->WriteHTML($stylesheet,1);
			$pdf->WriteHTML($html, 2); // write the HTML into the PDF
			$pdf->Output($pdfFilePath, 'D'); // save to file because we can
		}
	}
	
	function export_excel($data, $data_type)
	{
		//load our new PHPExcel library
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle($data['excel_data']['unique_title']);
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', $data['excel_data']['document_name']);
		//$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		
		//set aligment to center for that merged cell (A1 to D1)
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$this->excel->getActiveSheet()->setCellValue('A2', $data['excel_data']['Title']);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
		$this->excel->getActiveSheet()->getRowDimension(2)->setRowHeight(-1);
		if($data_type == "fat"){
			$this->excel->getActiveSheet()->mergeCells('A1:H1');
			$this->excel->getActiveSheet()->mergeCells('A2:H2');
			$this->excel->getActiveSheet()->setCellValue('A3', 'Attenuation Test');
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A3:D3');
			$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$this->excel->getActiveSheet()->setCellValue('E3', 'Equipment');
			$this->excel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('E3:H3');
			$this->excel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$this->excel->getActiveSheet()->setCellValue('A4', 'Number of Closures in the test section '. $data['project_responses']['fat_closures'] . ' pcs');
			$this->excel->getActiveSheet()->mergeCells('A4:D4');
			
			$this->excel->getActiveSheet()->setCellValue('E4', 'Cable type ' . $data['project_responses']['fat_cabletype']);
			$this->excel->getActiveSheet()->mergeCells('E4:H4');
			
			$this->excel->getActiveSheet()->setCellValue('A5', 'Number of splices in the test section ' . $data['project_responses']['fat_splices'] . ' pcs');
			$this->excel->getActiveSheet()->mergeCells('A5:D5');
			
			$this->excel->getActiveSheet()->setCellValue('E5', 'Wavelength ' . $data['project_responses']['fat_wavelength'] . 'nm');
			$this->excel->getActiveSheet()->mergeCells('E5:H5');
			
			$this->excel->getActiveSheet()->setCellValue('A6', 'Specification for FO-Cable ' . $data['project_responses']['fat_cablespecs'] . ' db/KM');
			$this->excel->getActiveSheet()->mergeCells('A6:D7');
			$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			
			$this->excel->getActiveSheet()->setCellValue('E6', 'Cable Length ' . $data['project_responses']['fat_cablelength'] . 'km');
			$this->excel->getActiveSheet()->mergeCells('E6:H6');
			
			$this->excel->getActiveSheet()->setCellValue('E7', 'Fibre Length ' . $data['project_responses']['fat_fibrelength'] . 'km');
			$this->excel->getActiveSheet()->mergeCells('E7:H7');
			
			$this->excel->getActiveSheet()->setCellValue('A8', 'Maximum splice loss ' . $data['project_responses']['fat_maxspiceloss'] . ' dB');
			$this->excel->getActiveSheet()->mergeCells('A8:D8');
			
			$this->excel->getActiveSheet()->setCellValue('E8', 'Test Date ' . date('dS F, Y', strtotime($data['project_responses']['fat_connectorloss'])));
			$this->excel->getActiveSheet()->mergeCells('E8:H8');
			
			$this->excel->getActiveSheet()->setCellValue('A9', 'Connector Loss ' . $data['project_responses']['fat_connectorloss'] . ' dB');
			$this->excel->getActiveSheet()->mergeCells('A9:D9');
			
			$this->excel->getActiveSheet()->setCellValue('E9', 'Maximum Attenuation ' . floatval(floatval($data['project_responses']['fat_cablespecs']) + floatval($data['project_responses']['fat_maxspiceloss'] + floatval($data['project_responses']['fat_connectorloss']))) . 'dB');
			$this->excel->getActiveSheet()->mergeCells('E9:H9');
			
			$this->excel->getActiveSheet()->setCellValue('A10', 'Fibre No.');
			$this->excel->getActiveSheet()->getStyle('A10')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A10:B11');
			$this->excel->getActiveSheet()->getStyle('A10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$this->excel->getActiveSheet()->setCellValue('C10', 'Attenuation in dB');
			$this->excel->getActiveSheet()->getStyle('C10')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('C10:D10');
			
			$this->excel->getActiveSheet()->setCellValue('E10', 'Average Attenuation in dB');
			$this->excel->getActiveSheet()->getStyle('E10')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('E10:F11');
			$this->excel->getActiveSheet()->getStyle('E10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$this->excel->getActiveSheet()->setCellValue('G10', 'Attenuation coefficient dB/KM');
			$this->excel->getActiveSheet()->getStyle('G10')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('G10:H11');
			$this->excel->getActiveSheet()->getStyle('G10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$this->excel->getActiveSheet()->setCellValue('C11', 'A-B');
			$this->excel->getActiveSheet()->getStyle('C11')->getFont()->setBold(true);
			
			$this->excel->getActiveSheet()->setCellValue('D11', 'B-A');
			$this->excel->getActiveSheet()->getStyle('D11')->getFont()->setBold(true);
			
			$counter = 12;
			foreach($data['project_responses']['Attenuation'] as $key => $value)
			{
				$average_attenuation = (floatval($value['fat_ab']) +  floatval($value['fat_ba']))/2;
				$attenuation_coefficient = $average_attenuation + floatval($data['project_responses']['fat_cablespecs']);
				$this->excel->getActiveSheet()->setCellValue('A' . $counter, $value['fat_fibrenumber']);
				$this->excel->getActiveSheet()->setCellValue('C' . $counter, $value['fat_ab']);
				$this->excel->getActiveSheet()->setCellValue('D' . $counter, $value['fat_ba']);
				$this->excel->getActiveSheet()->setCellValue('E' . $counter, $average_attenuation);
				$this->excel->getActiveSheet()->setCellValue('G' . $counter, $attenuation_coefficient);
				$counter++;
			}
			PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		}
		else
		{
			$value =  $this->excel->getActiveSheet()->getCell('A1')->getValue();
			$width = mb_strwidth ($value); //Return the width of the string
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth($width);
			//merge cell A1 until D1
			$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->mergeCells('A2:D2');
			if($data_type != "mss"){
				/*$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);*/
				for($col = 'B'; $col !== 'E'; $col++) {
					if($col != 'C'){
						$this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
					}
				}
			}
			$this->excel->getActiveSheet()->fromArray($data['excel_data']['actual_data'], null, 'A3');
		}
		//
		$filename= $data['excel_data']['unique_title']. '.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		             
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function read_excel_data($filename)
	{
		$this->load->library('excel');
		try {
			$inputFileType = PHPExcel_IOFactory::identify($filename);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($filename);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($filename,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();

		//  Loop through each row of the worksheet in turn
		for ($row = 1; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->toArray('A' . $row . ':' . $highestColumn . $row,
		                        NULL,
		                        TRUE,
		                        FALSE);
		}

		return $rowData;
	}

	function export_special_excel($data, $title)
	{
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setCellValue('A1', $title);
		$elements = count($data);
		$data_elements = $elements - 6;
		
		$data_limit = 7 + $data_elements;
		
		$this->excel->getActiveSheet()->fromArray($data, null, 'A2');
		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle("A5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle("A6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->mergeCells("A1:E1");

		$this->excel->getActiveSheet()->getStyle("A2:A4")->getFont()->setBold(true);

		$this->excel->getActiveSheet()->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight('15');


		$this->excel->getActiveSheet()->mergeCells("B2:E2");

		$this->excel->getActiveSheet()->mergeCells("B3:E3");

		$this->excel->getActiveSheet()->mergeCells("B4:E4");

		$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells("A5:E5");

		$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells("A6:E6");

		$this->excel->getActiveSheet()->getStyle("A7:E7")->getFont()->setBold(true);

		$this->excel->getActiveSheet()->getStyle("A8:D{$data_limit}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		foreach($this->excel->getActiveSheet()->getRowDimensions() as $rd) { $rd->setRowHeight(-1); }
		foreach(range('A','E') as $columnID) {
		    $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		$extended = $data_limit + 2;
		$this->excel->getActiveSheet()->SetCellValue("E{$extended}", "Total");
		$this->excel->getActiveSheet()->SetCellValue("F{$extended}","=SUM(F8:F{$data_limit})" );
		$filename= str_replace(" ", "_", $title) . '.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		         
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function quick_array_excel($data, $title)
	{
		$this->load->library('excel');

		$number_of_columns = count($data[0]);
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setCellValue('A1', $title);

		$this->excel->getActiveSheet()->fromArray($data, NULL, 'A2');

		$lastColumn = $this->excel->getActiveSheet()->getHighestColumn();

		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->mergeCells("A1:{$lastColumn}1");

		foreach(range('A', $lastColumn) as $columnID) {
		    $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

		$filename= str_replace(" ", "_", $title) . '.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		         
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}
}