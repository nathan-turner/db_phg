<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 


<xsl:template match="/listing_medschool_affils" >
  <tr>
    <td><img src="images/transpix.gif" /></td>
    <td class="cssBHead" nowrap="true"><xsl:value-of select="affiliation/type/@label" /></td>
    <td><img src="images/transpix.gif" /></td>
    <td class="cssBHead" nowrap="true"><xsl:value-of select="affiliation/med_school_name/@label" /></td>
  </tr>
 <tr><td colspan="4" height="5"><img src="images/transpix.gif" /></td></tr>

    <xsl:apply-templates select="affiliation" />

</xsl:template>


<xsl:template match="//affiliation">

  <tr>
    <xsl:apply-templates select="type" />
    <xsl:apply-templates select="med_school_name" />
  </tr>
 <tr><td colspan="4" height="3"><img src="images/transpix.gif" /></td></tr>
 <tr><td colspan="4" height="1" bgcolor="#000066"><img src="images/transpix.gif" /></td></tr>
 <tr><td colspan="4" height="3"><img src="images/transpix.gif" /></td></tr>

</xsl:template>



<xsl:template match="type">
  <td><img src="images/transpix.gif" /></td>
  <td class="csstext" nowrap="true"><xsl:value-of select="." /></td>
</xsl:template>

<xsl:template match="med_school_name">
  <td><img src="images/transpix.gif" /></td>
  <td class="csstext" nowrap="true"><xsl:value-of select="." /></td>
</xsl:template>

</xsl:stylesheet>


