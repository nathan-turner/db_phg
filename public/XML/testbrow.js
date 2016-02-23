/////////////////////////////////////////////////////////////////////////////
//////// HELPER FUNCTIONS -- MUST GO BEFORE sniff() BECAUSE NETSCAPE ////////
///////////// CHOKES ON THE ActiveX CONTROL AND WON'T LOAD //////////////////
///////////////// HELPER FUNCTIONS IF THEY COME AFTER ///////////////////////

////////////// BROWSER DETECT FUNCTIONS //////////////////////

function getBrowserInfo(){
  version = parseFloat(getFullVersion());
  OS = getFullOpSys();
}

function getFullVersion(){
  var startindex = detect.indexOf(" ", place) + 1;
    if (startindex==-1){ startindex = detect.indexOf("/", place) + 1; }
  var endindex = detect.indexOf(";", place);
//  alert ("In getFullVersion: startindex: " + startindex + "\nendindex: " + endindex)
  return detect.substring(startindex, endindex)
}

function getFullOpSys(){
  var startindex = useragent.indexOf(";", place) + 1;
  var endindex = useragent.indexOf(")", place);
//  alert ("In getFullOpSys: startindex: " + startindex + "\nendindex: " + endindex)
  return useragent.substring(startindex, endindex)
}

function checkIt(string){
  place = detect.indexOf(string) + 1;
  return place;
}

///////////////////////////////////////////////////////////

function sniff(){

    if(version>=5.5){ browser = true }
    ////////////////

    var xml = "<?xml version=\"1.0\" encoding=\"UTF-16\"?><cjb></cjb>";
    var xsl = "<?xml version=\"1.0\" encoding=\"UTF-16\"?><x:stylesheet version=\"1.0\" xmlns:x=\"http://www.w3.org/1999/XSL/Transform\" xmlns:m=\"urn:schemas-microsoft-com:xslt\"><x:template match=\"/\"><x:value-of select=\"system-property('m:version')\" /></x:template></x:stylesheet>";

    var x = null;
    var msxml2 = false
    var msxml2v26 = false
//    var msxml2v30 = false
    var msxml2v40 = false
    var msxml = false
//    var replacemode = false
//    var browser = false

  if(browser){
//    document.browser.src = "XML/images/chkd.jpg"
    try{ 
      x = new ActiveXObject("Msxml2.DOMDocument"); 
      x.async = false;
      if (x.loadXML(xml)){ msxml2 = true; }
    }catch(e){
//       alert("msxml2 bad: " + e.description)
    }

    try{ 
      x = new ActiveXObject("Msxml2.DOMDocument.2.6"); 
      x.async = false;
      if (x.loadXML(xml)){ msxml2v26 = true; }
    }catch(e){
//       alert("msxml2.6 bad: " + e.description)
    }

    try{ 
      x = new ActiveXObject("Msxml2.DOMDocument.3.0"); 
      x.async = false;
      if (x.loadXML(xml)){
        msxml2v30 = true;
//        document.msxml3.src = "XML/images/chkd.jpg"
    }
    }catch(e){
//      alert("msxml2v30 bad: " + e.description)
    }

    try{ 
      x = new ActiveXObject("Msxml2.DOMDocument.4.0"); 
      x.async = false;
      if (x.loadXML(xml)){ msxml2v40 = true; }
    }catch(e){
//       alert("msxml2v40 bad: " + e.description)
    }

    try{ 
      x = new ActiveXObject("Microsoft.XMLDOM");  
      x.async = false;
      if (x.loadXML(xml)){ msxml = true; }
    }catch(e){
//       alert("msxml bad: " + e.description)
    } 

    try{
      var s = new ActiveXObject("Microsoft.XMLDOM"); 
      s.async = false;
      if (s.loadXML(xsl)){
        try{
          var op = x.transformNode(s);
            if (op.indexOf("stylesheet") == -1){
//              document.modereplace.src = "XML/images/chkd.jpg"
              replacemode = true;
            }
        }catch(e){
//          alert("Side-By-Side")
        }
      }
    }catch(e){ alert(e.description) }
  }
//alert("msxml2: " + msxml2 + "\nmsxml2v26: " + msxml2v26 + "\nmsxml2v30: " + msxml2v30 + "\nmsxml2v40: " + msxml2v40 + "\nmsxml: " + msxml + "\nreplacemode: " + replacemode)
  if((!msxml2v30)||(!replacemode)||(!browser)){
    return false;
  }else{
    return true;
  }
}


///////// WRITEINSTALL() ////////////////////

function writeinstall(){
var strimg
document.write("<HTML>")
document.write("<HEAD><LINK REL=STYLESHEET TYPE=\"text/css\" HREF=\"../HTML/styles/gmecd.css\"><TITLE>Graduate Medical Education Library 2012-2013</TITLE></HEAD>")
document.write("<BODY marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 LINK=#005530 alink=#aa4433 vlink=#773377 bgcolor=#ffffff>")
document.write("<form name=\"installform\">")
document.write("<TABLE topmargin=0 border=0 cellpadding=0 cellspacing=0>")
document.write("  <tr>")
document.write("   <td width=5%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=10 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=23% height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=300 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=3%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=20 height=1></td>")
document.write("   <td width=3%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=58% height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=10 height=1></td>")
document.write("  </tr>")
document.write("  <tr><td colspan=9 height=108 bgcolor=#F8F8F8><img src=\"../HTML/images/headersm.jpg\" width=1600></td></tr>")
document.write("  <tr><td colspan=9 height=3 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=cssContentsHead>Evaluate/Install XML Software...</td>")
document.write("  </tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 CLASS=\"cssAHEAD\">")
document.write("<a href=\"../Tour/XML-Tour.htm\" target=\"_blank\">Evaluate XML features and benefits</a>")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 CLASS=\"csstext\">")
document.write("Click the link above for an explanation of the features and benefits of using the XML version of the GME Library.")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=15><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 CLASS=\"cssAHEAD\">")
document.write("Install XML software")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 CLASS=\"csstext\">")
document.write("The XML version of the Graduate Medical Education Library requires Internet Explorer version 5.5 or later. In addition, the XML interpreter (parser version 3.0) set in \"replace\" mode is required. When your computer opened this file, a check was performed to determine if required software is present.<BR>")
document.write("<b>If the word \"Fix\" appears before any of the buttons below, your system is not configured correctly.</b>")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("     <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=\"csstext\">")
document.write("These instructions pertain to computers running Windows98/2000/ME:")
document.write("<ul>")
document.write("<li>Follow the instructions for any step showing the word <B>Fix</B>.")
document.write("<li>Do the steps in order. Steps 4 and 5 must be done <b>AFTER</b> ")
document.write("      Steps 1, 2, and 3. This means, for example, that if you reinstall ")
document.write("      Internet Explorer 5.5, (or&nbsp;a later version), you must ")
document.write("      re-run Steps 2 through 5.")
document.write("<li>Be sure to <b>REBOOT</b> your computer after completing the required steps.")
document.write("</ul>")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=cssProg nowrap>")
document.write("      There are 5 Steps:")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=3><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=1 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td class=cssProg nowrap valign=top>1.</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"cssCHead\">Required Browser and version<br>")
document.write("      <FONT CLASS=\"csstext\">Microsoft Internet Explorer 5.5 or higher is required. Click the button at right to install.")
document.write("    </td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
if(browser) { strimg = "chkd.jpg" } else { strimg = "unchkd.jpg" }
document.write("    <td valign=top><img src=\"images/" + strimg + "\" name=\"browser\" width=\"60\" height=\"40\" alt=\"\" /></td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"csstext\"><input type=button onClick=\"javascript:installie55();\" value=\"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Install IE 5.5&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\">")
document.write("  </tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=1 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td class=cssProg nowrap valign=top>2.</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"cssCHead\">Required Software and version<br>")
document.write("      <FONT CLASS=\"csstext\">The Microsoft Parser (Interpreter) \"MSXML3\" must be installed. Click the button at right to install.")
document.write("    </td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
if(msxml2v30) { strimg = "chkd.jpg" } else { strimg = "unchkd.jpg" }
document.write("    <td valign=top><img src=\"images/" + strimg + "\" name=\"msxml3\" width=\"60\" height=\"40\" alt=\"\" /></td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"csstext\"><input type=button onClick=\"javascript:installmsxml3();\" value=\"Install MSXML3 Parser\">")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=1 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td class=cssProg nowrap valign=top>3.</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"cssProg\">Refresh this page</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"csstext\"><input type=button onClick=\"javascript:document.execCommand('refresh');\" value=\"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Click to Refresh&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\">")
document.write("  </tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=1 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td class=cssProg nowrap valign=top>4.</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"cssCHead\">Required Software Configuration<br>")
document.write("      <FONT CLASS=\"csstext\">The MSXML3 Parser must be set in \"Replace\" Mode (<i>NOT</i> Side-by-Side mode). Click the button at right to update your settings.")
document.write("    </td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
if(replacemode) { strimg = "chkd.jpg" } else { strimg = "unchkd.jpg" }
document.write("    <td valign=top><img src=\"images/" + strimg + "\" name=\"modereplace\" width=\"60\" height=\"40\" alt=\"\" /></td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"csstext\"><input type=button onClick=\"javascript:installxmlinst();\" value=\"&nbsp;Set to Replace mode&nbsp;\">")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=1 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=5><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td class=\"cssProg\" nowrap valign=top>5.</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"cssProg\">Reboot!</td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("  </tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=3 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
writeHTML();
}

///////// WRITESTANDARD() ////////////////////

function writestandard(){
var strimg
document.write("<HTML>")
document.write("<HEAD><LINK REL=STYLESHEET TYPE=\"text/css\" HREF=\"../HTML/styles/gmecd.css\"><TITLE>Graduate Medical Education Library 2012-2013</TITLE></HEAD>")
document.write("<BODY marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 LINK=#005530 alink=#aa4433 vlink=#773377 bgcolor=#ffffff>")
document.write("<form name=\"installform\">")
document.write("<TABLE topmargin=0 border=0 cellpadding=0 cellspacing=0>")
document.write("  <tr>")
document.write("   <td width=5%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=10 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=23% height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=300 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=3%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=2%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=3%  height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=5 height=1></td>")
document.write("   <td width=58% height=1 bgcolor=#3F5D53><img src=\"images/transpix.gif\" width=10 height=1></td>")
document.write("  </tr>")
document.write("  <tr><td colspan=9 height=108 bgcolor=#3F5D53><img src=\"../HTML/images/headersm.jpg\" width=1600></td></tr>")
document.write("  <tr><td colspan=9 height=3 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=\"cssContentsHead\">Go to XML Version...</td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=5 CLASS=\"csstext\">")
document.write("Go to the XML version of the <I>Graduate Medical Education Library</I>. This version allows sorting, ")
document.write("searching, side-by-side comparisons between programs, and other advanced features.<BR>")
document.write("<FONT CLASS=\"cssCHEAD\"><a href=\"../Tour/XML_Tour.htm\" target=\"blank\">Tour XML features</a></FONT>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td CLASS=\"csstext\">")
document.write("<input type=button onClick=\"javascript:location.href='default.htm'\" value=\"&nbsp;&nbsp;&nbsp;&nbsp;Go to XML Version&nbsp;&nbsp;&nbsp;\">")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=3 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
writeHTML();
}

///////// WRITEHTML() ////////////////////

function writeHTML(){
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=\"cssContentsHead\">...Or Go to HTML Version</td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=5 CLASS=\"csstext\">")
document.write("The HTML version of the <I>Graduate Medical Education Library</I> contains all the data in the XML version, but it only requires the commonly installed browser configurations. ")
document.write("    </td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"csstext\">")
document.write("<input type=button onClick=\"javascript:location.href='../HTML/default.htm'\" value=\"&nbsp;&nbsp;Go to HTML Version&nbsp;&nbsp;\">")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=3 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=\"cssAHEAD\">...Or Open the GME Directory Archive</td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=5 CLASS=\"csstext\">")
document.write("To view the PDF files of the <I>Graduate Medical Education Directory</I> from 1996 to the current edition, click \"Open GME Directory Archive.\" ")
document.write("If this button fails to operate, close your browser, navigate to and explore the \"GMED\" subdirectory on this CD, and double-click the file \"HistGMED.pdf\". In Microsoft Windows<sup>&reg;</sup>7, select \"Open\" or \"Open in New Window\" to explore the root directory or to open files from the root directory.<BR><BR>")
document.write("<B><I>Operating note:</I></B> There is a searchable Acrobat index attached to the \"Welcome.pdf\" file for each year of publication. To use it, open the Welcome file with the Adobe Reader software (not the browser plugin).<BR><BR>")

document.write("    <td><img src=\"images/transpix.gif\"></td><td CLASS=\"csstext\">")
document.write("  <a href=\"../GMED/HistGMED.pdf\" target=\"blank\"><img src=\"../HTML/images/gme-pdf.jpg\"></a>")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=7 class=\"cssCHEAD\">Install Adobe Acrobat Reader</td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td><img src=\"images/transpix.gif\"></td><td colspan=5 CLASS=\"csstext\">")
document.write("If your computer doesn't have Adobe Reader, click the button to the right to go to the Adobe Web site to download the appropriate version of the Adobe Reader for your computer.")
document.write("    <td><img src=\"images/transpix.gif\"></td><td CLASS=\"csstext\">")
document.write("  <a href=\"http://get.adobe.com/reader/otherversions/\" target=\"blank\"><img src=\"../HTML/images/inst-acro.jpg\"></a>")
document.write("    </td>")
document.write("  </tr>")
document.write("  <tr>")
document.write("    <td colspan=1><img src=\"images/transpix.gif\"></td><td colspan=7 CLASS=\"csstext\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td colspan=9 height=3 bgcolor=#aa0060><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td height=10><img src=\"images/transpix.gif\"></td></tr>")
document.write("  <tr><td>&nbsp;</td><td colspan=7 CLASS=\"csstext\"><I><B>Operational notes:</B></I><BR>")
document.write("<B>1.</B> Many computers have programs that block the execution of links to external Web sites (pop-up blockers). If your computer has such a program, hold down the \"Ctrl\" or control key while clicking a link to temporarily suspend the blocking function.<BR><BR>")
document.write("<B>2.</B> To reset all visited links to their starting color (green), click \"Tools\" on the browser menu bar and select \"Internet Options\" in the drop-down menu. In Internet Options &rarr; Browsing History &rarr; Delete button &rarr; Delete History button.<BR><BR>")
document.write("<B>3.</B> In Microsoft Windows<sup>&reg;</sup>7, if you launch the program from <B><I>Devices with Removable Storage</I></B> (ie, CD or DVD drive), you will be asked: \"Do you want to allow the following program from an unknown publisher to make changes to this program?\" Select \"Yes\" to run the program.<BR><BR>")
document.write("<B>4.</B> In Microsoft Windows<sup>&reg;</sup>7, your computer requires at least 4GB of hardware memory (RAM) for Internet Explorer to open a new window for Adobe Reader. As a result, you may see the message \"Cannot use Adobe Reader to view PDF in your web browser. Reader will now exit. Please exit your browser and try again.\" To resolve this issue, navigate to the root directory of the CD, locate the file you want in either the \"GMED\" or \"HTML\" subdirectories, and double-click it to launch Adobe Reader in its own window.<BR><BR>")
document.write("    </td>")
document.write("    <td><img src=\"images/transpix.gif\"></td>")
document.write("    <td CLASS=\"csstext\">")
document.write("")
document.write("    </td>")
document.write("  </tr>")
document.write("</TABLE>")
document.write("</form>")
document.write("</BODY>")
document.write("</HTML>")
}

/////////////////////////////////////////////////////////////
function installie55(){
  location.href="../ie6/ie6setup.exe"
}

function installmsxml3(){
  location.href="../plugin/msxml3.exe"
}

function installxmlinst(){
  location.href="../plugin/xmlinst.exe"
}
/////////////////////////////////////////////////////////////

var now = new Date();
// fix the bug in Navigator 2.0, Macintosh
fixDate(now);
// cookie expires in one year (actually, 365 days)
// 365 days in a year
// 24 hours in a day
// 60 minutes in an hour
// 60 seconds in a minute
// 1000 milliseconds in a second
now.setTime(now.getTime() + 365 * 24 * 60 * 60 * 1000);

function CheckCookie(cookiein,frame,licfile){
/////  alert("blicagree: " + blicagree + "\nlicfile: " + licfile)
  if(frame=="text"){ frame="data"; }
  if(!blicagree){
    var strlicagree = getCookie(cookiein)
    if(strlicagree!="true"){
      MM_nbGroup('down','navbar1','i_r2c1','images/slices/i_r2c1f3.gif',1)
      top[frame].location.href = licfile
      return "false"
    }
    else{
      blicagree = true
      return "true"
    }
  } else{ return "true" }
}

function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "; expires=" + now.toGMTString()) +
//      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
  blicagree = true
  top.header.MM_nbGroup('down','navbar1','i_r2c1','images/slices/i_r2c1f3.gif',1)
  top.data.location.href="intro.htm"
}

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" + 
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}

function fixDate(date) {
  var base = new Date(0);
  var skew = base.getTime();
  if (skew > 0)
    date.setTime(date.getTime() - skew);
}


