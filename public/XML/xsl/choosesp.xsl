<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="specialty_listings">
<html><head>
<STYLE>
  BODY {margin:0}
  .csstext {font: 9pt Arial, Helvetica, sans-serif;text-align:left;}
  .csstextred {font: 9pt Arial, Helvetica, sans-serif;text-align:left;color:#dd0000;}
  .csstextcenter {font: 9pt Arial, Helvetica, sans-serif;text-align:center;}
  .cssproghead {font: 10pt Arial, Helvetica, sans-serif;text-align:left;font-weight:700;color:#006A4D}
  .cssBHEAD {font:11pt UniversCond,helvetica,arial,verdana, sans-serif; font-weight:700;}
  .csslistbox {font: 10pt arial,helvetica,verdana, sans-serif;}
</STYLE>
</head>



<SCRIPT><xsl:comment><![CDATA[
//var srchprogram=getqstringvalue("srchprogram",window.location.search)
//srchprogram = URLdecode(srchprogram,"%26","&")
//var nodecount = 0

function doOnLoad(){
//  srchprogram = URLdecode(srchprogram," ","%20")
//  srchprogram = URLdecode(srchprogram,"&","%26")
//  top.btnbar.location.href = "btnbarsp.htm?selectscreen=choosesp&states="+states+"&srchprogram="+srchprogram+"&nodecount="+nodecount
    top.btnbar.location.href = "../btnbarsp.htm?selectscreen=choosesp"
  //// TAB UP CODE IN BTNBARSP ////
}

function showspecialtyscreen(){
  var qstring = ""
  for(i=0;i<document.forms.length;i++){
    if(document.forms[i].selectspeccheckbox.checked || document.forms[i].selectspeccheckbox.checked=="true"){
      if(qstring!=""){ qstring+="," }
      qstring += document.forms[i].name
//      alert(qstring)
    }
  }

  if(qstring != ""){
//    alert("specialty.htm?specdisplay="+qstring)
    location.href="specialty.htm?specdisplay="+qstring
  }
  else{
    alert("Please check at least one Specialty.")
  }
}

///////////////////////////////////////////////////////////////////////////
function getqstringvalue(name,inpara){

  var indexname = inpara.indexOf(name)
  if(indexname!=-1){
    var indexvalue    = inpara.indexOf("=",inpara.indexOf(name)) + 1
    var indexvalueend = inpara.indexOf("&",indexvalue)
    if(indexvalueend==-1){
      indexvalueend=inpara.length
    }
      return inpara.substring(indexvalue,indexvalueend)
  }
  else{
    return "'" + name + "' doesn't exist."
  }
}
///////////////////////////////////////////////////////////////////////////

]]></xsl:comment></SCRIPT>


<body onLoad="doOnLoad();" bgcolor="#eeeeee" LINK="#006A4D" VLINK="#773377" ALINK="#aa4433">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="82%" height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
  </tr>
  <tr>
    <td class="cssproghead" nowrap="true"><img src="../images/transpix.gif" height="1" width="5" />Count</td>
    <td><img src="../images/transpix.gif" height="1" width="5" /></td>
    <td class="cssproghead" nowrap="true">Code<img src="../images/transpix.gif" height="1" width="15" /></td>
    <td colspan="3" class="cssproghead" nowrap="true">Specialty</td>
    <td><img src="../images/transpix.gif" /></td>
  </tr>

<xsl:apply-templates select="specialty">
    <xsl:sort select="specialty_name" />
</xsl:apply-templates>

</table>
</body>
</html>
</xsl:template>



<xsl:template name="dir_emailurl">
  <xsl:param name="emailloc"></xsl:param>
    <td class="csstext" valign="top"><a href="mailto:{$emailloc}"><xsl:value-of select="." /></a><img src="images/transpix.gif" /></td>
</xsl:template>

<xsl:template match="specialty">
<xsl:variable name="strspecid">
  <xsl:value-of select="child::specialty_id" />
</xsl:variable>
<xsl:variable name="progrequrl">
../../html/<xsl:value-of select="specialty_name/@ProgReq" />
</xsl:variable>
<xsl:variable name="strcensus">
  <xsl:value-of select="child::census" />
</xsl:variable>
<xsl:variable name="strPRlinktext">
  <xsl:choose>
    <xsl:when test="$strspecid&gt;=700">
      <xsl:choose>
        <xsl:when test="$strspecid&lt;800">
          Specialty Description
        </xsl:when>
        <xsl:otherwise>
          General Information
        </xsl:otherwise>
      </xsl:choose>
    </xsl:when>
    <xsl:when test="$strspecid&gt;=540">
      General Information
    </xsl:when>
    <xsl:otherwise>
      General Information
    </xsl:otherwise>
  </xsl:choose>
</xsl:variable>


  <tr><td colspan="10" height="5"><img src="../images/transpix.gif" /></td></tr>
  <tr><td colspan="10" height="1" bgcolor="#000066"><img src="../images/transpix.gif" /></td></tr>
  <tr>
<xsl:choose>
<xsl:when test="$strspecid='380'">
    <td bgcolor="#ffffff" class="csstextred" nowrap="true"><center><xsl:value-of select="$strcensus" /></center></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td bgcolor="#ffffff" class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td class="csstext" bgcolor="#ffffff"><img src="../images/transpix.gif" height="1" width="1" /></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="csstext" nowrap="true" bgcolor="#ffffff"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" bgcolor="#ffffff"><a href="javascript:top.btnbar.openprogreq('{$progrequrl}','{$strspecid}')">General Information</a></td>
</xsl:when>
<xsl:when test="$strspecid='381'">
    <form name="{$strspecid}">
    <td bgcolor="#ffffff" class="csstextred" nowrap="true"><center><xsl:value-of select="$strcensus" /></center></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td bgcolor="#ffffff" class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td class="csstext" bgcolor="#ffffff"><xsl:if test="$strcensus>0"><input type="checkbox" name="selectspeccheckbox" class="csstext" /></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="csstext" nowrap="true" bgcolor="#ffffff"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text><I>   </I></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" bgcolor="#ffffff"><a href="javascript:top.btnbar.openprogreq('{$progrequrl}','{$strspecid}')">General Information</a></td>
    </form>
</xsl:when>
<xsl:when test="$strspecid='382'">
    <form name="{$strspecid}">
    <td bgcolor="#ffffff" class="csstextred" nowrap="true"><center><xsl:value-of select="$strcensus" /></center></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td bgcolor="#ffffff" class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td class="csstext" bgcolor="#ffffff"><xsl:if test="$strcensus>0"><input type="checkbox" name="selectspeccheckbox" class="csstext" /></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="csstext" nowrap="true" bgcolor="#ffffff"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text><I>   </I></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" bgcolor="#ffffff"><a href="javascript:top.btnbar.openprogreq('{$progrequrl}','{$strspecid}')">General Information</a></td>
    </form>
</xsl:when>
<xsl:when test="$strspecid='383'">
    <form name="{$strspecid}">
    <td bgcolor="#ffffff" class="csstextred" nowrap="true"><center><xsl:value-of select="$strcensus" /></center></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td bgcolor="#ffffff" class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td class="csstext" bgcolor="#ffffff"><xsl:if test="$strcensus>0"><input type="checkbox" name="selectspeccheckbox" class="csstext" /></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="csstext" nowrap="true" bgcolor="#ffffff"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text><I>   </I></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" bgcolor="#ffffff"><a href="javascript:top.btnbar.openprogreq('{$progrequrl}','{$strspecid}')">General Information</a></td>
    </form>
</xsl:when>
<xsl:when test="$strspecid='384'">
    <form name="{$strspecid}">
    <td bgcolor="#ffffff" class="csstextred" nowrap="true"><center><xsl:value-of select="$strcensus" /></center></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td bgcolor="#ffffff" class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td class="csstext" bgcolor="#ffffff"><xsl:if test="$strcensus>0"><input type="checkbox" name="selectspeccheckbox" class="csstext" /></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="csstext" nowrap="true" bgcolor="#ffffff"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text><I>   </I></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" bgcolor="#ffffff"><a href="javascript:top.btnbar.openprogreq('{$progrequrl}','{$strspecid}')">General Information</a></td>
    </form>
</xsl:when>
<xsl:otherwise>
    <form name="{$strspecid}">
    <td bgcolor="#ffffff" class="csstextred" nowrap="true"><center><xsl:value-of select="$strcensus" /></center></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td bgcolor="#ffffff" class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
<xsl:choose>
  <xsl:when test="$strcensus=0">
    <td class="csstext" bgcolor="#ffffff"><input type="checkbox" name="selectspeccheckbox" class="csstext" disabled="true"/></td>
  </xsl:when>
  <xsl:otherwise>
    <td class="csstext" bgcolor="#ffffff"><input type="checkbox" name="selectspeccheckbox" class="csstext" /></td>
  </xsl:otherwise>
</xsl:choose>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="csstext" nowrap="true" bgcolor="#ffffff"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text><I>   </I></xsl:if></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" bgcolor="#ffffff"><a href="javascript:top.btnbar.openprogreq('{$progrequrl}','{$strspecid}')"><xsl:value-of select="$strPRlinktext" /></a></td>
    </form>
</xsl:otherwise>
</xsl:choose>
</tr>

<xsl:apply-templates select="subspecialty">
    <xsl:sort select="specialty_name" />
</xsl:apply-templates>

</xsl:template>

<xsl:template match="subspecialty">
<xsl:variable name="strspecid">
  <xsl:value-of select="child::specialty_id" />
</xsl:variable>
<xsl:variable name="strcensus">
  <xsl:value-of select="child::census" />
</xsl:variable>


  <tr><form name="{$strspecid}">
    <td class="csstextred" nowrap="true"><center><xsl:value-of select="child::census" /></center></td>
    <td><img src="../images/transpix.gif" /></td>
    <td class="csstextred"><img src="../images/transpix.gif" height="1" width="10" />
      <a name="{$strspecid}"><xsl:value-of select="spec_ltr_code" /></a><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td colspan="5" valign="top">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
<xsl:choose>
  <xsl:when test="$strcensus=0">
          <td width="2%" class="csstext"><input type="checkbox" name="selectspeccheckbox" class="csstext" disabled="true" /></td>
  </xsl:when>
  <xsl:otherwise>
          <td width="2%" class="csstext"><input type="checkbox" name="selectspeccheckbox" class="csstext"/></td>
  </xsl:otherwise>
</xsl:choose>
          <td width="2%"><img src="../images/transpix.gif" height="1" width="5" /></td>
          <td width="96%" class="csstext" nowrap="true" valign="top"><xsl:value-of select="specialty_name" /><xsl:if test="$strcensus=0"><xsl:text> </xsl:text><I>   </I></xsl:if></td>
        </tr>
      </table>
    </td>
  </form></tr>
</xsl:template>

</xsl:stylesheet>


