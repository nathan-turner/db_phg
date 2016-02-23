<?php

/**
 * Create list styles
 *
 *
 * @category   Phpdocx
 * @package    elements
 * @copyright  Copyright (c) 2009-2013 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    3.4
 * @link       http://www.phpdocx.com
 * @since      File available since Release 3.4
 */

class CreateListStyle
{

    /**
     * @access protected
     * @var string
     */
    protected $_xml;
    /**
     * @access private
     * @var CreateStyle
     * @static
     */
    private static $_instance = NULL;

    /**
     * Construct
     *
     * @access public
     */
    public function __construct()
    {
        
    }

    /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        
    }

    /**
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->_xml;
    }
    
    /**
     *
     * @access public
     * @param string $name
     * @param array $styleOptions
     * @return string
     */
    public function addListStyle($name, $styleOptions)
    {
        //Set default
        foreach($styleOptions as $index => $value){
            if(empty($value['type'])){
                $styleOptions[$index]['type'] = 'decimal';
            }
            if(empty($value['format'])){
                $styleOptions[$index]['format'] = '%' . ($index + 1) .'.';
            }
            if(empty($value['hanging'])){
                $styleOptions[$index]['hanging'] = 360;
            }
            if(empty($value['left'])){
                $styleOptions[$index]['left'] = 720*($index + 1);
            }
        }
        
   
        //Repeat ciclically if not defined up to level 9
        $entries = count($styleOptions);
        if($entries < 9){
            for($k = $entries; $k < 9; $k++){
               $styleOptions[$k]['type'] = $styleOptions[$k%$entries]['type'];
               $styleOptions[$k]['format'] = '%' . ($k + 1) .'.';
               $styleOptions[$k]['hanging'] = 360;
               $styleOptions[$k]['left'] = 720*($k + 1);
            }
        }
        $baseList = '<w:abstractNum w:abstractNumId="" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" >
                        <w:multiLevelType w:val="hybridMultilevel"/>
                        <w:lvl w:ilvl="0" w:tplc="">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_0#"/>
                            <w:lvlText w:val="#levelFormat_0#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_0#" w:hanging="#levelHanging_0#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="1" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_1#"/>
                            <w:lvlText w:val="#levelFormat_1#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_1#" w:hanging="#levelHanging_1#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="2" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_2#"/>
                            <w:lvlText w:val="#levelFormat_2#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_2#" w:hanging="#levelHanging_2#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="3" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_3#"/>
                            <w:lvlText w:val="#levelFormat_3#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_3#" w:hanging="#levelHanging_3#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="4" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_4#"/>
                            <w:lvlText w:val="#levelFormat_4#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_4#" w:hanging="#levelHanging_4#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="5" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_5#"/>
                            <w:lvlText w:val="#levelFormat_5#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_5#" w:hanging="#levelHanging_5#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="6" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_6#"/>
                            <w:lvlText w:val="#levelFormat_6#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_6#" w:hanging="#levelHanging_6#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="7" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_7#"/>
                            <w:lvlText w:val="#levelFormat_7#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_7#" w:hanging="#levelHanging_7#"/>
                            </w:pPr>
                        </w:lvl>
                        <w:lvl w:ilvl="8" w:tplc="" w:tentative="1">
                            <w:start w:val="1"/>
                            <w:numFmt w:val="#levelStyle_8#"/>
                            <w:lvlText w:val="#levelFormat_2#"/>
                            <w:lvlJc w:val="left"/>
                            <w:pPr>
                                <w:ind w:left="#levelLeft_8#" w:hanging="#levelHanging_8#"/>
                            </w:pPr>
                        </w:lvl>
                    </w:abstractNum>';
        
        for($j = 0; $j < 9; $j++){
            $baseList = str_replace('#levelStyle_'. $j .'#', $styleOptions[$j]['type'], $baseList);
            $baseList = str_replace('#levelFormat_'. $j .'#', $styleOptions[$j]['format'], $baseList);
            $baseList = str_replace('#levelHanging_'. $j .'#', (string) $styleOptions[$j]['hanging'], $baseList);
            $baseList = str_replace('#levelLeft_'. $j .'#', (string) $styleOptions[$j]['left'], $baseList);
        }
        return $baseList;
        
    }

    

}
