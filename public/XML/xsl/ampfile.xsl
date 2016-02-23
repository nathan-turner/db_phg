<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" />

<xsl:template match="/program_labels">
    <xsl:apply-templates select="label" />
</xsl:template>


<xsl:template match="label">
  <xsl:apply-templates select="heading" />
</xsl:template>



<xsl:template match="heading">
  <xsl:variable name="strheading">
    <xsl:copy-of select="." />
  </xsl:variable>
  <xsl:variable name="strinnertext">
    <xsl:copy-of select="following-sibling::innertext" />
  </xsl:variable>
<html><head><link rel="stylesheet" type="text/css" href="../html/styles/gmecd.css" />
<title><xsl:value-of select="$strheading" /></title></head>
<body bgcolor="#eeeeee" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" link="#006A4D" vlink="#773377" alink="#aa4433">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td width="3%"  height="1" bgcolor="#000000"><img src="images/transpix.gif" height="1" width="10" /></td>
   <td width="97%" height="1" bgcolor="#000000"><img src="images/transpix.gif" height="1" width="10" /></td>
 </tr>


  <tr><td colspan="2" height="32" background="images/backblnd.jpg"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2" height="1"  bgcolor="#000000"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2" height="1"  bgcolor="#cccccc"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2" height="1"  bgcolor="#ffffff"><img src="images/transpix.gif" /></td></tr>

  <tr><td colspan="2" height="5"><img src="../images/transpix.gif" height="5" width="1" /></td></tr>
  <tr>
    <td><img src="images/transpix.gif" /></td>
    <td CLASS="cssContentsHead"><xsl:copy-of select="$strheading" /></td>
  </tr>
  <tr><td colspan="2" height="10"><img src="images/transpix.gif" height="10" width="1" /></td></tr>

  <!--  INNERTEXT  -->

  <tr>
    <td><img src="images/transpix.gif" /></td>
    <td CLASS="csstext" valign="top" colspan="1"><xsl:copy-of select="$strinnertext" /></td>
  </tr>



</table>
</body>
</html>
</xsl:template>



<!--     DON'T SHOW     -->
<xsl:template match="ampfile">
</xsl:template>

<xsl:template match="innertext">
</xsl:template>
<!--  END DON'T SHOW    -->

</xsl:stylesheet>


