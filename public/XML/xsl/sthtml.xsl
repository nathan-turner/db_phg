<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 


<xsl:template match="institution">
<xsl:variable name="Inst_idurl">
  <xsl:value-of select="inst_id" />
</xsl:variable>


 <tr><td colspan="4" height="5"><img src="images/transpix.gif" /></td></tr>
 <tr><td colspan="4" height="1" bgcolor="#000066"><img src="images/transpix.gif" /></td></tr>
 <tr><td bgcolor="#ffffff"><img src="images/transpix.gif" /></td><td colspan="3" class="cssBHEAD" bgcolor="#ffffff"><xsl:value-of select="Inst_Name" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstext"><xsl:value-of select="Address_1" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstext"><xsl:value-of select="Address_2" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstext"><xsl:value-of select="Address_3" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstext"><xsl:value-of select="Where" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstext"><i><xsl:value-of select="inst_id/@label" /></i><xsl:text> </xsl:text><xsl:value-of select="inst_id" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstext"><a href="javascript:top.btnbar.openmedschaffil('{$Inst_idurl}');"><xsl:value-of select="med_sch_affil/@label" /></a></td></tr>

  <xsl:apply-templates select="sponsors" />

  <xsl:apply-templates select="participating" />

  <xsl:apply-templates select="clin_site" />

</xsl:template>




<xsl:template match="sponsors">
 <tr><td colspan="4" height="3"><img src="images/transpix.gif" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstextred"><b><xsl:value-of select="@label" /></b></td></tr>
  <xsl:apply-templates select="spec" />
</xsl:template>

<xsl:template match="participating">
 <tr><td colspan="4" height="3"><img src="images/transpix.gif" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstextred"><b><xsl:value-of select="@label" /></b></td></tr>
  <xsl:apply-templates select="spec" />
</xsl:template>

<xsl:template match="clin_site">
 <tr><td colspan="4" height="3"><img src="images/transpix.gif" /></td></tr>
 <tr><td><img src="images/transpix.gif" /></td><td colspan="3" class="csstextred"><b><xsl:value-of select="@label" /></b></td></tr>
  <xsl:apply-templates select="spec" />
</xsl:template>


<xsl:template match="spec">
 <tr><form name="{prog_id}">
  <td><img src="images/transpix.gif" /></td>
  <td><img src="images/transpix.gif" />
      <input type="hidden" name="specialty_id" value="{specialty_id}" /></td>
  <td><input type="checkbox" name="detailcheckbox" class="csstext" /></td>
  <td class="csstext" nowrap="true"><xsl:value-of select="specialty" /><xsl:text> </xsl:text><font class="csstextred"><xsl:value-of select="prog_id/@name" /></font></td>
</form></tr>
</xsl:template>

</xsl:stylesheet>


