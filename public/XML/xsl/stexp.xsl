<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="listing_cities">
<xsl:apply-templates select="state">
    <xsl:sort select="state_name" />
</xsl:apply-templates>

</xsl:template>



<xsl:template match="state">
  <tr><a name='{state_abbrev}' /><td colspan="4" height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="4" height="6" bgcolor="#e8e8e8"><img src="images/transpix.gif" /></td></tr>
  <tr>
    <td bgcolor="#ffffff"><img src="images/transpix.gif" height="1" width="20" />
      <!-- <a href="javascript:stcnt('{state_abbrev}');"><img src="images/minus.gif" height="19" width="19" border="0" /></a><img src="images/transpix.gif" height="1" width="20" /> -->
    </td>
    <td colspan="3" class="cssstatehead" nowrap="true" valign="top" bgcolor="#ffffff"><b><xsl:value-of select="state_name" /></b></td>
  </tr>

<xsl:apply-templates select="city"/>

</xsl:template>



<xsl:template match="city">
  <tr>
    <td><img src="images/transpix.gif" /></td>
    <td colspan="3" class="csstextred" nowrap="true" valign="top"><b><xsl:value-of select="city_name" /></b></td>
  </tr>

<xsl:apply-templates select="institution"/>

</xsl:template>



<xsl:template match="institution">
  <tr><form name="{institution_id}">
    <td><img src="images/transpix.gif" /></td>
    <td><input type="checkbox" name="selectspeccheckbox" class="csstext" /></td>
    <td><img src="images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" valign="top"><xsl:value-of select="institution_name" /></td>
  </form></tr>
</xsl:template>

</xsl:stylesheet>


