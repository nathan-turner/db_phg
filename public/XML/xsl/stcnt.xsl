<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="listing_cities">
<xsl:apply-templates select="state">
    <xsl:sort select="state_name" />
</xsl:apply-templates>

</xsl:template>



<xsl:template match="state">
  <tr><a name='{state_abbrev}' /><td colspan="4" height="3"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="4" height="10" bgcolor="#e8e8e8"><img src="images/transpix.gif" /></td></tr>
  <tr><form name="{state_abbrev}">
    <td><img src="images/transpix.gif" height="1" width="40" />
      <input type="checkbox" name="selectstatecheckbox" class="csstext" /> </td>
      <!-- <a href="javascript:stexp('{state_abbrev}');"><img src="images/plus.gif" height="19" width="19" border="0" /></a><img src="images/transpix.gif" height="1" width="20" /></td> -->
    <td colspan="3" class="cssstatehead" nowrap="true"> <b><xsl:value-of select="state_name" /></b></td>
  </form></tr>

</xsl:template>



</xsl:stylesheet>


