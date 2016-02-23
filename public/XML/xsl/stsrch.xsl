<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 


<xsl:template match="institution">
  <tr><form name="{institution_id}">
    <td><img src="images/transpix.gif" /></td>
    <td><input type="checkbox" name="selectspeccheckbox" class="csstext" /></td>
    <td><img src="images/transpix.gif" /></td>
    <td class="csstext" nowrap="true" valign="top"><xsl:value-of select="institution_name" /><xsl:text> </xsl:text>
      <font class="csstextred">(<xsl:value-of select="parent::city/city_name" />,<xsl:text> </xsl:text><xsl:value-of select="parent::city/state_name" />)</font>
    </td>
  </form></tr>
</xsl:template>


</xsl:stylesheet>


