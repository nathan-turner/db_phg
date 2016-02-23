<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 


<xsl:template match="/" >
  <xsl:apply-templates select="//program">
  </xsl:apply-templates>
  
</xsl:template>


<xsl:template match="program">
<form name="{prog_id}">
<tr>
    <td width="10"><img src="images/transpix.gif" width="10" height="1" alt="" /></td>
  <td class="csstext" nowrap="true" valign="top"><a HREF="javascript:top.btnbar.reselect('state','{state}');"><font color="#0000dd"><xsl:value-of select="state" /></font></a></td>
    <td width="10"><img src="images/transpix.gif" width="10" height="1" alt="" /></td>
  <td class="csstext" nowrap="true" valign="top"><a HREF="javascript:top.btnbar.reselect('city','{city}');"><font color="#0000dd"><xsl:value-of select="city" /></font></a></td>
    <td width="40"><img src="images/transpix.gif" width="40" height="1" alt="" /></td>
<!--
  <td class="csstext" nowrap="true" valign="top"><a HREF="javascript:top.btnbar.reselect('positions','{positions}');"><font color="#0000dd"><xsl:value-of select="positions" /></font></a></td>
    <td width="10"><img src="images/transpix.gif" width="10" height="1" alt="" /></td>
-->
  <td><input type="checkbox" name="detailcheckbox" class="csstext" /></td>
    <td><img src="images/transpix.gif" width="1" height="1" alt="" /></td>
  <td class="csstextred" nowrap="true" valign="top"><xsl:value-of select="spec_ltr_code" /></td>
    <td><img src="images/transpix.gif" width="1" height="1" alt="" /></td>
  <td class="csstext" nowrap="true" valign="top"><xsl:value-of select="prog_name" /></td>
</tr>
  </form>
</xsl:template>


</xsl:stylesheet>


