<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="listing_cities">
<html><head>
<STYLE>
  BODY {margin:0}
  .csstext {font: 9pt Arial, Helvetica, sans-serif;text-align:left;}
  .csstextred {font: 9pt Arial, Helvetica, sans-serif;text-align:left;color:#dd0000;}
  .csstextcenter {font: 9pt Arial, Helvetica, sans-serif;text-align:center;}
  .cssproghead {font: 10pt Arial, Helvetica, sans-serif;text-align:left;font-weight:700;color:#229922}
  .cssBHEAD {font:11pt UniversCond,helvetica,arial,verdana, sans-serif; font-weight:700;}
  .csslistbox {font: 10pt arial,helvetica,verdana, sans-serif;}
</STYLE>
</head>



<SCRIPT><xsl:comment><![CDATA[
function doOnLoad(){
  top.btnbar.location.href = "../btnbarst.htm?selectscreen=choosest"
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


<body onLoad="doOnLoad();" bgcolor="#eeeeee" LINK="#008840" VLINK="#008840" ALINK="#008840">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5" alt="" /></td>
    <td width="2%"  height="1"><img src="../images/transpix.gif" height="1" width="5"  alt="" /></td>
    <td width="90%" height="1"><img src="../images/transpix.gif" height="1" width="1" alt="" /></td>
  </tr>

<xsl:apply-templates select="state">
    <xsl:sort select="state_name" />
</xsl:apply-templates>

</table>
</body>
</html>
</xsl:template>



<xsl:template match="state">
  <tr><td colspan="6" height="5"><img src="../images/transpix.gif" /></td></tr>
  <tr><td colspan="6" height="1" bgcolor="#000066"><img src="../images/transpix.gif" /></td></tr>
  <tr>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" height="1" width="20" />
      <img src="../images/minus.gif" height="9" width="9" /></td>
    <td class="csstext" bgcolor="#ffffff"><img src="../images/transpix.gif" height="1" width="15" /></td>
    <td bgcolor="#ffffff"><img src="../images/transpix.gif" /></td>
    <td colspan="3" class="cssBHEAD" nowrap="true" valign="top" bgcolor="#ffffff"><xsl:value-of select="state_name" /></td>
  </tr>

<xsl:apply-templates select="city"/>

</xsl:template>



<xsl:template match="city">
  <tr>
    <td><img src="../images/transpix.gif" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td class="csstextred" nowrap="true" valign="top"><b><xsl:value-of select="city_name" /></b></td>
  </tr>

<xsl:apply-templates select="institution"/>

</xsl:template>



<xsl:template match="institution">
  <tr><form name="{institution_id}">
    <td><img src="../images/transpix.gif" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td><input type="checkbox" name="selectspeccheckbox" class="csstext" /></td>
    <td><img src="../images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" valign="top"><xsl:value-of select="institution_name" /></td>
  </form></tr>
</xsl:template>

</xsl:stylesheet>


