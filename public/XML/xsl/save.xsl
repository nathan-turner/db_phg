<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 


<xsl:template match="/listing_programs">
  <xsl:apply-templates select="program" />
</xsl:template>

<xsl:template match="program">
  <xsl:apply-templates select="specialty" />
  <xsl:apply-templates select="prog_name" />
  <xsl:apply-templates select="setting" />
  <xsl:apply-templates select="prog_length" />
  <xsl:apply-templates select="years_required" />
  <xsl:apply-templates select="positions" />
  <xsl:apply-templates select="positions_gy1" />
  <xsl:apply-templates select="prev_gme_req" />
  <xsl:apply-templates select="prev_yrs_req" />

  <xsl:apply-templates select="hours_week" />
  <xsl:apply-templates select="hours_consec" />
  <xsl:apply-templates select="most_taxing" />
  <xsl:apply-templates select="duration" />
  <xsl:apply-templates select="moonlighting" />

  <xsl:apply-templates select="salary" />
  <xsl:apply-templates select="child" />
  <xsl:apply-templates select="pt_shared" />
  <xsl:apply-templates select="multi_start" />

  <xsl:apply-templates select="qual_imp" />
  <xsl:apply-templates select="international" />
  <xsl:apply-templates select="retreats" />
  <xsl:apply-templates select="offcampus" />
  <xsl:apply-templates select="hospice" />
  <xsl:apply-templates select="cultcomp" />
  <xsl:apply-templates select="nonenglish" />
  <xsl:apply-templates select="altcomp" />
  <xsl:apply-templates select="research" />

  <xsl:apply-templates select="MPH_MBA" />
  <xsl:apply-templates select="PhD" />
  <xsl:apply-templates select="additional_trng" />
  <xsl:apply-templates select="req_add_trng" />
  <xsl:apply-templates select="add_trng_length" />

  <xsl:apply-templates select="int_model" /> 

  <xsl:apply-templates select="Prevmedarea" />
  <xsl:apply-templates select="military" />
  <xsl:apply-templates select="subspec" />
  <xsl:apply-templates select="sponspec" />
  <xsl:apply-templates select="FREIDA" />


  <xsl:apply-templates select="sponsor" />
  <xsl:apply-templates select="clin_site" />
  <xsl:apply-templates select="participating_inst" />
  <xsl:apply-templates select="prog_id" />
  <xsl:apply-templates select="director" />
  <xsl:apply-templates select="codirector" />
  <xsl:apply-templates select="address" />
  <xsl:apply-templates select="dir_email" />
  <xsl:apply-templates select="website" />


  <tr>
    <td><img src="images/transpix.gif" /></td>
    <td><img src="images/transpix.gif" /></td>
    <td class="csstextright"><a href="javascript:top.header.deletelisting('{prog_id}');">Delete Listing</a><img src="images/transpix.gif" width="20" height="1" /></td>
  </tr>
  <tr><td colspan="3" height="10"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="3" height="1"><hr /></td></tr>
</xsl:template>


<xsl:template match="specialty">
  <tr><td colspan="3" class="cssspechead" valign="top"><xsl:value-of select="." /></td></tr>
</xsl:template>

<xsl:template match="prog_name">
  <tr><td colspan="3" class="cssBHead" valign="top"><xsl:value-of select="." /></td></tr>
</xsl:template>

<xsl:template match="clin_site|//prog_id|//codirector|//prog_length|//years_required|//positions|//positions_gy1|//hours_consec|//most_taxing|//duration|//moonlighting|//child|//pt_shared|//multi_start|//international|//retreats|//offcampus|//hospice|//cultcomp|//nonenglish|//altcomp|//research|//PhD|//additional_trng|//req_add_trng|//add_trng_length|//sponspec|//prev_med|//military|//subspec|//int_model">
  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>

<xsl:template match="participating_inst">
  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:apply-templates select="particips" /></td></tr>
</xsl:template>

<xsl:template match="particips">
<xsl:value-of select="." /><br />
</xsl:template>


<xsl:template match="prev_yrs_req">
  <xsl:variable name="strreq">
    <xsl:value-of select="preceding-sibling::prev_gme_req" />
  </xsl:variable>
  <xsl:variable name="stryrsreq">
    <xsl:value-of select="." />
  </xsl:variable>

  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext">
  <xsl:choose>
    <xsl:when test="$stryrsreq!=''" >
      <xsl:value-of select="$stryrsreq" /><xsl:if test="contains($strreq,'exceptions')" ><xsl:text> </xsl:text>(with exceptions)</xsl:if><img src="images/transpix.gif" />
    </xsl:when>
    <xsl:otherwise >
      No<xsl:if test="contains($strreq,'exceptions')" ><xsl:text> </xsl:text>(with exceptions)</xsl:if><img src="images/transpix.gif" />
    </xsl:otherwise>
  </xsl:choose>
      </td></tr>
</xsl:template>

<xsl:template match="FREIDA">
  <xsl:variable name="strurl">
    <xsl:value-of select="." />
  </xsl:variable>
  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext">
  <xsl:choose>
    <xsl:when test="$strurl!=''">
      <xsl:call-template name="FREIDAurl">
        <xsl:with-param name="urlloc"><xsl:value-of select="$strurl" /></xsl:with-param>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>    
      <img src="images/transpix.gif" />
    </xsl:otherwise>
  </xsl:choose>
  </td></tr>
</xsl:template>

<xsl:template match="director">
  <tr><td height="5"><img src="images/transpix.gif" width="1" height="5" /></td></tr>
  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><B><xsl:value-of select="." /></B></td></tr>
</xsl:template>

<!-- === TEMPLATES === -->

<xsl:template name="websiteurl">
  <xsl:param name="urlloc"></xsl:param>
  <tr><td class="csstextredright" nowrap="true" valign="top">Website:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><a href="{$urlloc}" target="_blank"><b><xsl:value-of select="." /></b></a><img src="images/transpix.gif" /></td></tr>
</xsl:template>

<xsl:template name="dir_emailurl">
  <xsl:param name="emailloc"></xsl:param>
  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><a href="mailto:{$emailloc}"><xsl:value-of select="." /></a><img src="images/transpix.gif" /></td></tr>
</xsl:template>

<xsl:template name="FREIDAurl">
  <xsl:param name="urlloc"></xsl:param>
    <a href="{$urlloc}" target="_blank"><xsl:value-of select="." /></a><img src="images/transpix.gif" />
</xsl:template>
<!-- === END TEMPLATES === -->

<xsl:template match="website">
  <xsl:variable name="strwebsite">
    <xsl:value-of select="." />
  </xsl:variable>
  <xsl:if test="$strwebsite!=''">
    <xsl:call-template name="websiteurl">
      <xsl:with-param name="urlloc"><xsl:value-of select="." /></xsl:with-param>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template match="dir_email">
  <xsl:variable name="stremail">
    <xsl:value-of select="." />
  </xsl:variable>
  <xsl:if test="$stremail!=''">
    <xsl:call-template name="dir_emailurl">
      <xsl:with-param name="emailloc"><xsl:value-of select="." /></xsl:with-param>
    </xsl:call-template>
  </xsl:if>
</xsl:template>




<xsl:template match="address">
  <xsl:variable name="strdirphone">
    <xsl:value-of select="following-sibling::dir_phone" />
  </xsl:variable>
  <xsl:variable name="strdirfax">
    <xsl:value-of select="following-sibling::dir_fax" />
  </xsl:variable>
  <tr><td class="csstextredright" nowrap="true" valign="top"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext">
    <xsl:apply-templates select="address1" />
    <xsl:apply-templates select="address2" />

  <xsl:variable name="straddress3">
    <xsl:value-of select="following-sibling::address3" />
  </xsl:variable>
  <xsl:choose>
    <xsl:when test="$straddress3!=''">
      <xsl:apply-templates select="address3" />
    </xsl:when>
  </xsl:choose>

    <xsl:apply-templates select="dir_city" />
    <xsl:apply-templates select="dir_state" />
    <xsl:apply-templates select="dir_zip" />
    <i>Tel:</i><xsl:text> </xsl:text><xsl:value-of select="$strdirphone" />
  <xsl:choose>
    <xsl:when test="$strdirfax!=''">
      <img src="../images/transpix.gif" height="1" width="25" /><i>Fax:</i><xsl:text> </xsl:text><xsl:value-of select="$strdirfax" /><br />
    </xsl:when>
  </xsl:choose>
  </td></tr>
</xsl:template>


<xsl:template match="address1">
  <xsl:value-of select="." /><br />
</xsl:template>
<xsl:template match="address2">
  <xsl:value-of select="." /><br />
</xsl:template>
<xsl:template match="address3">
    <xsl:value-of select="." /><br />
</xsl:template>


<xsl:template match="dir_city">
  <xsl:value-of select="." />,<xsl:text> </xsl:text>
</xsl:template>
<xsl:template match="dir_state">
  <xsl:value-of select="." /><xsl:text> </xsl:text>
</xsl:template>
<xsl:template match="dir_zip">
   <xsl:value-of select="." /><br />
</xsl:template>



<!--           SUBHEADS                 -->
<!--  Program Information -->
<xsl:template match="setting">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Program Information</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>

<!--  Work Schedule -->
<xsl:template match="hours_week">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Work Schedule</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>

<!--  Employment Policies/Benefits -->
<xsl:template match="salary">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Employment Policies/Benefits</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext">$<xsl:number value="." grouping-separator="," grouping-size="3" /></td></tr>
</xsl:template>

<!--  Educational Curriculum/Features -->
<xsl:template match="qual_imp">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Educational Curriculum/Features</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>

<!--  Additional Training -->
<xsl:template match="MPH_MBA">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Additional Training</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>

<!--  Other Information -->
<xsl:template match="Prevmedarea">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Other Information</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>

<!--  Contact Information -->
<xsl:template match="sponsor">
  <tr><td height="5"><img src="images/transpix.gif" /></td></tr>
  <tr><td colspan="2"><img src="images/transpix.gif" /></td><td class="csstextblue"><b>Contact Information</b></td></tr>
  <tr><td class="csstextredright" nowrap="true"><xsl:value-of select="@label" />:</td>
      <td class="csstext"><img src="images/transpix.gif" /></td>
      <td class="csstext"><xsl:value-of select="." /></td></tr>
</xsl:template>




<!-- LEAVE OUT THESE ITEMS -->
<xsl:template match="//apps_recd">
</xsl:template>
<xsl:template match="//pp_comm">
</xsl:template>
<xsl:template match="//prev_gme_req">
</xsl:template>


<xsl:template match="//apps_recd/@label">
</xsl:template>
<xsl:template match="//pp_comm/@label">
</xsl:template>
<!--   END LEAVE OUTS     -->

</xsl:stylesheet>




