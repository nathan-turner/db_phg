<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 




<xsl:template match="//prog_name">
  <xsl:variable name="strurl">
    <xsl:value-of select="following-sibling::website" />
  </xsl:variable>

  <td class="cssproghead" valign="top">

  <xsl:choose>
    <xsl:when test="$strurl!=''">
      <xsl:call-template name="websiteurl">
        <xsl:with-param name="urlloc"><xsl:value-of select="$strurl" /></xsl:with-param>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>    
      <b><xsl:value-of select="." /></b>
    </xsl:otherwise>
  </xsl:choose>

  </td>
</xsl:template>

<xsl:template match="//specialty">
  <td class="csstextred" valign="top"><b><xsl:value-of select="." /></b></td>
</xsl:template>

<xsl:template match="//prog_idsavenode">
  <xsl:variable name="specialty_id">
    <xsl:value-of select="following-sibling::specialty_id" />
  </xsl:variable>
  <xsl:variable name="savenode">
    <xsl:value-of select="." />
  </xsl:variable>

  <td class="csstextred" valign="top">
    <a href="javascript:top.header.savelisting('{$savenode}','{$specialty_id}');">Save listing</a>
  </td>
</xsl:template>



<xsl:template match="clin_site|//prog_id|//codirector|//sponsor|//setting|//prog_length|//years_required|//offers_prelim_pos|//positions|//positions_gy1|//hours_week|//hours_consec|//moonlighting|//child|//pt_shared|//multi_start|//qual_imp|//international|//retreats|//offcampus|//hospice|//cultcomp|//nonenglish|//altcomp|//research|//MPH_MBA_PhD|//additional_trng|//req_add_trng|//add_trng_length|//sponspec|//prev_med|//Prevmedarea|//military|//subspec|//accepting_apps|//night_float|//moving_allow|//housing_stip|//meal_allow|//parking|//pdas|//job_place|//cross_cov|//major_med|//depend_med|//ment_hlth|//in_ment_hlth|//group_life|//dental_ins|//dis_ins|//dis_ins_HIV|//ins_begins|//paid_leave|//unpaid_leave|//med_profess|//debt_mgmt|//teach_skills|//mentoring|//teamwork|//tobacco|//abuse|//primary|//rural|//women|//hospitalist|//research_trck|//other|//exam_req|//surveys|//portfolio|//degree|//osce">
  <td class="csstext" valign="top">
    <xsl:value-of select="." /><img src="images/transpix.gif" />
  </td>
</xsl:template>
 
<!-- === TEMPLATES === -->

<xsl:template name="websiteurl">
  <xsl:param name="urlloc"></xsl:param>
    <a href="{$urlloc}" target="_blank"><b><xsl:value-of select="." /></b></a><img src="images/transpix.gif" />
</xsl:template>

<xsl:template name="dir_emailurl">
  <xsl:param name="emailloc"></xsl:param>
    <td class="csstext" valign="top"><a href="mailto:{$emailloc}"><xsl:value-of select="." /></a><img src="images/transpix.gif" /></td>
</xsl:template>

<xsl:template name="FREIDAurl">
  <xsl:param name="urlloc"></xsl:param>
    <a href="https://freida.ama-assn.org/Freida/user/viewProgramSearch.do" target="_blank">Click and enter Prgm Number</a><img src="images/transpix.gif" />
</xsl:template>
<!-- === END TEMPLATES === -->


<xsl:template match="//most_taxing">
  <xsl:variable name="strduration">
    <xsl:value-of select="following-sibling::duration" />
  </xsl:variable>

  <td class="csstext" valign="top">
      <xsl:value-of select="." /><img src="images/transpix.gif" /><br /><xsl:value-of select="$strduration" />
  </td>
</xsl:template>

<xsl:template match="//prev_yrs_req">
  <xsl:variable name="strreq">
    <xsl:value-of select="preceding-sibling::prev_gme_req" />
  </xsl:variable>
  <xsl:variable name="stryrsreq">
    <xsl:value-of select="." />
  </xsl:variable>

  <td class="csstext" valign="top">
  <xsl:choose>
    <xsl:when test="$stryrsreq!=''" >
      <xsl:value-of select="$stryrsreq" /><xsl:if test="contains($strreq,'exceptions')" ><xsl:text> </xsl:text>(with exceptions)</xsl:if><img src="images/transpix.gif" />
    </xsl:when>
    <xsl:otherwise >
      No<xsl:if test="contains($strreq,'exceptions')" ><xsl:text> </xsl:text>(with exceptions)</xsl:if><img src="images/transpix.gif" />
    </xsl:otherwise>
  </xsl:choose>
  </td>
</xsl:template>


<xsl:template match="//FREIDA">
  <xsl:variable name="strurl">
    <xsl:value-of select="." />
  </xsl:variable>
  <td class="csstext" valign="top">
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
  </td>
</xsl:template>


<xsl:template match="//director">
  <xsl:variable name="strdiremail">
    <xsl:value-of select="following-sibling::dir_email" />
  </xsl:variable>

  <xsl:choose>
    <xsl:when test="$strdiremail!=''">
      <td class="csstext" valign="top"><a href="mailto:{$strdiremail}"><xsl:value-of select="." /></a><img src="images/transpix.gif" /></td>
    </xsl:when>
    <xsl:otherwise>    
      <td class="csstext" valign="top"><xsl:value-of select="." /><img src="images/transpix.gif" /></td>
    </xsl:otherwise>
  </xsl:choose>

</xsl:template>


<xsl:template match="//participating_inst">
  <td class="csstext" valign="top">
    <xsl:apply-templates select="particips" />
  </td>
</xsl:template>
<xsl:template match="particips">
  <xsl:value-of select="." /><br />
</xsl:template>

<xsl:template match="//positions">
  <td class="csstext" valign="top"><xsl:number value="." grouping-separator="," grouping-size="3" /><img src="images/transpix.gif" /></td>
</xsl:template>

<xsl:template match="//salary">
  <xsl:variable name="strsalary" >
    <xsl:value-of select="." />
  </xsl:variable>
  <xsl:choose>
    <xsl:when test="$strsalary=''">
      <td class="csstext" valign="top"><img src="images/transpix.gif" /></td>
    </xsl:when>
    <xsl:otherwise>
    <td class="csstext" valign="top">$<xsl:number value="$strsalary" grouping-separator="," grouping-size="3" /><img src="images/transpix.gif" /></td>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>





<xsl:template match="//address">
  <xsl:variable name="strdirphone">
    <xsl:value-of select="following-sibling::dir_phone" />
  </xsl:variable>
  <xsl:variable name="strdirfax">
    <xsl:value-of select="following-sibling::dir_fax" />
  </xsl:variable>

  <td class="csstext" valign="top">
    <xsl:apply-templates select="address1" />
    <xsl:apply-templates select="address2" />
    <xsl:apply-templates select="address3" />

    <xsl:apply-templates select="dir_city" />
    <xsl:apply-templates select="dir_state" />
    <xsl:apply-templates select="dir_zip" />
    <i>Tel:</i><xsl:text> </xsl:text><xsl:value-of select="$strdirphone" />
  <xsl:choose>
    <xsl:when test="$strdirfax!=''">
      <br /><i>Fax:</i><xsl:text> </xsl:text><xsl:value-of select="$strdirfax" /><br />
    </xsl:when>
  </xsl:choose>
  </td>
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



<!-- LEAVE OUT THESE ITEMS -->
<xsl:template match="//specialty_id">
</xsl:template>
<xsl:template match="//state">
</xsl:template>
<xsl:template match="//city">
</xsl:template>
<xsl:template match="//dir_phone">
</xsl:template>
<xsl:template match="//dir_fax">
</xsl:template>
<xsl:template match="//dir_email">
</xsl:template>
<xsl:template match="//website">
</xsl:template>
<xsl:template match="//prev_gme_req">
</xsl:template>
<xsl:template match="//duration">
</xsl:template>



<!--   END LEAVE OUTS     -->

</xsl:stylesheet>




