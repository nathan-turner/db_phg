<?php 
				
$filename="client_activity-".date('Y-m-d').".xls";
if($filename!="" )
{
	$data = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Sheet 1</x:Name>
                    <x:WorksheetOptions>
                        <x:Print>
                            <x:ValidPrinterInfo/>
                        </x:Print>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
	<style type="text/css">
	table td { border: 1px solid }
	</style>
</head>

<body>
   '.str_replace('&nbsp;','',$_POST["list"]).'
</body></html>';

header( "Content-Type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=\"$filename\"" );
//$file_to_download = $file;//"XML/images/Old Database documentation.docx";//$file;
//readfile($file_to_download);
echo $data;
}

?>