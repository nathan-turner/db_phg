<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" omit-xml-declaration="yes" /> 


<xsl:template match="/" >
  <xsl:apply-templates select="//program" />
</xsl:template>

<xsl:template match="program" >
<program>
  <xsl:apply-templates select="specialty" />
  <xsl:apply-templates select="prog_id" mode="savenode" />
  <xsl:apply-templates select="specialty_id" />
  <xsl:apply-templates select="prog_name" />

  <xsl:apply-templates select="setting" />
  <xsl:apply-templates select="prog_length" />
  <xsl:apply-templates select="years_required" />
  <xsl:apply-templates select="offers_prelim_pos" />
  <xsl:apply-templates select="positions" />
  <xsl:apply-templates select="positions_gy1" />
  <xsl:apply-templates select="prev_gme_req" />
  <xsl:apply-templates select="prev_yrs_req" />
  <xsl:apply-templates select="accepting_apps" />
  <xsl:apply-templates select="multi_start" />

  <xsl:apply-templates select="hours_week" />
  <xsl:apply-templates select="hours_consec" />
  <xsl:apply-templates select="most_taxing" />
  <xsl:apply-templates select="duration" />
  <xsl:apply-templates select="moonlighting" />
  <xsl:apply-templates select="night_float" />

  <xsl:apply-templates select="salary" />
  <xsl:apply-templates select="pt_shared" />
  <xsl:apply-templates select="child" />
  <xsl:apply-templates select="moving_allow" />
  <xsl:apply-templates select="housing_stip" />
  <xsl:apply-templates select="meal_allow" />
  <xsl:apply-templates select="parking" />
  <xsl:apply-templates select="pdas" />
  <xsl:apply-templates select="job_place" />
  <xsl:apply-templates select="cross_cov" />

  <xsl:apply-templates select="major_med" />
  <xsl:apply-templates select="depend_med" />
  <xsl:apply-templates select="ment_hlth" />
  <xsl:apply-templates select="in_ment_hlth" />
  <xsl:apply-templates select="group_life" />
  <xsl:apply-templates select="dent_ins" />
  <xsl:apply-templates select="dis_ins" />
  <xsl:apply-templates select="dis_ins_HIV" />
  <xsl:apply-templates select="ins_begins" />
  <xsl:apply-templates select="paid_leave" />
  <xsl:apply-templates select="unpaid_leave" />

  <xsl:apply-templates select="med_profess" />
  <xsl:apply-templates select="debt_mgmt" />
  <xsl:apply-templates select="teach_skills" />
  <xsl:apply-templates select="mentoring" />
  <xsl:apply-templates select="teamwork" />
  <xsl:apply-templates select="qual_imp" />
  <xsl:apply-templates select="international" />
  <xsl:apply-templates select="retreats" />
  <xsl:apply-templates select="offcampus" />
  <xsl:apply-templates select="hospice" />
  <xsl:apply-templates select="cultcomp" />
  <xsl:apply-templates select="nonenglish" />
  <xsl:apply-templates select="altcomp" />
  <xsl:apply-templates select="abuse" />
  <xsl:apply-templates select="research" />

  <xsl:apply-templates select="MPH_MBA_PhD" />
  <xsl:apply-templates select="additional_trng" />
  <xsl:apply-templates select="req_add_trng" />
  <xsl:apply-templates select="add_trng_length" />
  <xsl:apply-templates select="primary" />
  <xsl:apply-templates select="rural" />
  <xsl:apply-templates select="women" />
  <xsl:apply-templates select="hospitalist" />
  <xsl:apply-templates select="research_trck" />
  <xsl:apply-templates select="other" />
  <xsl:apply-templates select="exam_req" />
  <xsl:apply-templates select="surveys" />
  <xsl:apply-templates select="portfolio" />
  <xsl:apply-templates select="degree" />
  <xsl:apply-templates select="osce" />

  <xsl:apply-templates select="military" />
  <xsl:apply-templates select="subspec" />
  <xsl:apply-templates select="sponspec" />
  <xsl:apply-templates select="FREIDA" />


  <xsl:apply-templates select="sponsor" />
<!--  <xsl:apply-templates select="clin_site" /> -->
  <xsl:apply-templates select="participating_inst" />
  <xsl:apply-templates select="prog_id" mode="reg" />
  <xsl:apply-templates select="director" />
  <xsl:apply-templates select="codirector" />
  <xsl:apply-templates select="address" />
  <xsl:apply-templates select="dir_phone" />
  <xsl:apply-templates select="dir_fax" />
  <xsl:apply-templates select="dir_email" />
  <xsl:apply-templates select="website" />
</program>
</xsl:template>


<xsl:template match="prog_name" >
  <prog_name label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></prog_name>
</xsl:template>

  <xsl:template match="address">
<address label="{@label}">
    <xsl:apply-templates select="address1" />
    <xsl:apply-templates select="address2" />
    <xsl:apply-templates select="address3" />
    <xsl:apply-templates select="dir_city" />
    <xsl:apply-templates select="dir_state" />
    <xsl:apply-templates select="dir_zip" />
</address>
  </xsl:template>

<!-- FIELDS -->
<xsl:template match="specialty" >
  <specialty><xsl:value-of select="." /></specialty>
</xsl:template>
<xsl:template match="prog_id" mode="savenode" >
  <prog_idsavenode><xsl:value-of select="." /></prog_idsavenode>
</xsl:template>
<xsl:template match="specialty_id">
  <specialty_id><xsl:value-of select="." /></specialty_id>
</xsl:template>

<xsl:template match="sponsor" >
  <sponsor label="Sponsoring Institution" ampfile="sponsor"><xsl:value-of select="." /></sponsor>
</xsl:template>



<xsl:template match="participating_inst">
  <xsl:variable name="strclinsite" >
    <xsl:value-of select="following-sibling::clin_site" />
  </xsl:variable>
  <participating_inst label="Major participating institution(s)" ampfile="partic">
  <xsl:if test="$strclinsite!=''">
    <xsl:apply-templates select="following-sibling::clin_site" />
  </xsl:if>
<!--      <xsl:apply-templates select="following-sibling::clin_site" /> -->
      <xsl:apply-templates select="particips" />
  </participating_inst>
</xsl:template>
  <xsl:template match="clin_site" >
    <particips label="{@label}"><xsl:value-of select="." /></particips>
  </xsl:template>
  <xsl:template match="particips" >
    <particips><xsl:value-of select="." /></particips>
  </xsl:template>

<xsl:template match="prog_id" mode="reg" >
  <prog_id label="{@label}"><xsl:value-of select="." /></prog_id>
</xsl:template>

<xsl:template match="director" >
  <director label="{@label}"><xsl:value-of select="." /></director>
</xsl:template>
<xsl:template match="codirector" >
  <codirector label="{@label}"><xsl:value-of select="." /></codirector>
</xsl:template>
<xsl:template match="dir_phone" >
  <dir_phone label="{@label}"><xsl:value-of select="." /></dir_phone>
</xsl:template>
<xsl:template match="dir_fax" >
  <dir_fax label="{@label}"><xsl:value-of select="." /></dir_fax>
</xsl:template>
<xsl:template match="website" >
  <website><xsl:value-of select="." /></website>
</xsl:template>
<xsl:template match="dir_email" >
  <dir_email label="{@label}"><xsl:value-of select="." /></dir_email>
</xsl:template>
<xsl:template match="Prevmedarea" >
  <Prevmedarea label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></Prevmedarea>
</xsl:template>
<xsl:template match="military" >
  <military label="Military" ampfile="{@ampfile}"><xsl:value-of select="." /></military>
</xsl:template>
<xsl:template match="subspec" >
  <subspec label="{@label}" ampfile="subspec"><xsl:value-of select="." /></subspec>
</xsl:template>
<xsl:template match="FREIDA" >
  <FREIDA label="{@label}"><xsl:value-of select="." /></FREIDA>
</xsl:template>
<xsl:template match="sponspec" >
  <sponspec label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></sponspec>
</xsl:template>
<xsl:template match="prog_length" >
  <prog_length label="Program length" ampfile="{@ampfile}"><xsl:value-of select="." /></prog_length>
</xsl:template>
<xsl:template match="years_required" >
  <years_required label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></years_required>
</xsl:template>
<xsl:template match="positions" >
  <positions label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></positions>
</xsl:template>
<xsl:template match="positions_gy1" >
  <positions_gy1 label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></positions_gy1>
</xsl:template>

<xsl:template match="setting" >
  <setting label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></setting>
</xsl:template>
<xsl:template match="prev_gme_req" >
  <prev_gme_req><xsl:value-of select="." /></prev_gme_req>
</xsl:template>
<xsl:template match="prev_yrs_req" >
  <prev_yrs_req label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></prev_yrs_req>
</xsl:template>
<xsl:template match="offers_prelim_pos" >
  <offers_prelim_pos label="{@label}"><xsl:value-of select="." /></offers_prelim_pos>
</xsl:template>
<xsl:template match="accepting_apps" >
  <accepting_apps label="Accepting applications for 12-13"><xsl:value-of select="." /></accepting_apps>
</xsl:template>
<xsl:template match="night_float" >
  <night_float label="{@label}"><xsl:value-of select="." /></night_float>
</xsl:template>

<xsl:template match="hours_week" >
  <hours_week label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></hours_week>
</xsl:template>
<xsl:template match="hours_consec" >
  <hours_consec label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></hours_consec>
</xsl:template>
<xsl:template match="most_taxing" >
  <most_taxing label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></most_taxing>
</xsl:template>
<xsl:template match="duration" >
  <duration label="{@label}"><xsl:value-of select="." /></duration>
</xsl:template>
<xsl:template match="moonlighting" >
  <moonlighting label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></moonlighting>
</xsl:template>

<xsl:template match="salary" >
  <salary label="{@label}"><xsl:value-of select="." /></salary>
</xsl:template>
<xsl:template match="child" >
  <child label="{@label}"><xsl:value-of select="." /></child>
</xsl:template>
<xsl:template match="moving_allow" >
  <moving_allow label="{@label}"><xsl:value-of select="." /></moving_allow>
</xsl:template>
<xsl:template match="housing_stip" >
  <housing_stip label="{@label}"><xsl:value-of select="." /></housing_stip>
</xsl:template>
<xsl:template match="meal_allow" >
  <meal_allow label="{@label}"><xsl:value-of select="." /></meal_allow>
</xsl:template>
<xsl:template match="parking" >
  <parking label="{@label}"><xsl:value-of select="." /></parking>
</xsl:template>
<xsl:template match="pdas" >
  <pdas label="{@label}"><xsl:value-of select="." /></pdas>
</xsl:template>
<xsl:template match="job_place" >
  <job_place label="{@label}"><xsl:value-of select="." /></job_place>
</xsl:template>
<xsl:template match="cross_cov" >
  <cross_cov label="{@label}"><xsl:value-of select="." /></cross_cov>
</xsl:template>

<xsl:template match="pt_shared" >
  <pt_shared label="{@label}"><xsl:value-of select="." /></pt_shared>
</xsl:template>
<xsl:template match="multi_start" >
  <multi_start label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></multi_start>
</xsl:template>

<xsl:template match="major_med" >
  <major_med label="{@label}"><xsl:value-of select="." /></major_med>
</xsl:template>
<xsl:template match="depend_med" >
  <depend_med label="{@label}"><xsl:value-of select="." /></depend_med>
</xsl:template>
<xsl:template match="ment_hlth" >
  <ment_hlth label="{@label}"><xsl:value-of select="." /></ment_hlth>
</xsl:template>
<xsl:template match="in_ment_hlth" >
  <in_ment_hlth label="{@label}"><xsl:value-of select="." /></in_ment_hlth>
</xsl:template>
<xsl:template match="group_life" >
  <group_life label="{@label}"><xsl:value-of select="." /></group_life>
</xsl:template>
<xsl:template match="dent_ins" >
  <dent_ins label="{@label}"><xsl:value-of select="." /></dent_ins>
</xsl:template>
<xsl:template match="dis_ins" >
  <dis_ins label="{@label}"><xsl:value-of select="." /></dis_ins>
</xsl:template>
<xsl:template match="dis_ins_HIV" >
  <dis_ins_HIV label="{@label}"><xsl:value-of select="." /></dis_ins_HIV>
</xsl:template>
<xsl:template match="ins_begins" >
  <ins_begins label="{@label}"><xsl:value-of select="." /></ins_begins>
</xsl:template>
<xsl:template match="paid_leave" >
  <paid_leave label="{@label}"><xsl:value-of select="." /></paid_leave>
</xsl:template>
<xsl:template match="unpaid_leave" >
  <unpaid_leave label="{@label}"><xsl:value-of select="." /></unpaid_leave>
</xsl:template>

<xsl:template match="med_profess" >
  <med_profess label="{@label}"><xsl:value-of select="." /></med_profess>
</xsl:template>
<xsl:template match="debt_mgmt" >
  <debt_mgmt label="{@label}"><xsl:value-of select="." /></debt_mgmt>
</xsl:template>
<xsl:template match="teach_skills" >
  <teach_skills label="{@label}"><xsl:value-of select="." /></teach_skills>
</xsl:template>
<xsl:template match="mentoring" >
  <mentoring label="{@label}"><xsl:value-of select="." /></mentoring>
</xsl:template>
<xsl:template match="teamwork" >
  <teamwork label="{@label}"><xsl:value-of select="." /></teamwork>
</xsl:template>

<xsl:template match="qual_imp" >
  <qual_imp label="{@label}"><xsl:value-of select="." /></qual_imp>
</xsl:template>
<xsl:template match="international" >
  <international label="{@label}"><xsl:value-of select="." /></international>
</xsl:template>
<xsl:template match="retreats" >
  <retreats label="{@label}"><xsl:value-of select="." /></retreats>
</xsl:template>
<xsl:template match="offcampus" >
  <offcampus label="{@label}"><xsl:value-of select="." /></offcampus>
</xsl:template>
<xsl:template match="hospice" >
  <hospice label="{@label}"><xsl:value-of select="." /></hospice>
</xsl:template>
<xsl:template match="cultcomp" >
  <cultcomp label="{@label}"><xsl:value-of select="." /></cultcomp>
</xsl:template>
<xsl:template match="nonenglish" >
  <nonenglish label="{@label}"><xsl:value-of select="." /></nonenglish>
</xsl:template>
<xsl:template match="altcomp" >
  <altcomp label="{@label}"><xsl:value-of select="." /></altcomp>
</xsl:template>
<xsl:template match="abuse" >
  <abuse label="{@label}"><xsl:value-of select="." /></abuse>
</xsl:template>
<xsl:template match="tobacco" >
  <tobacco label="{@label}"><xsl:value-of select="." /></tobacco>
</xsl:template>

<xsl:template match="research" >
  <research label="{@label}"><xsl:value-of select="." /></research>
</xsl:template>

<xsl:template match="MPH_MBA_PhD" >
  <MPH_MBA_PhD label="{@label}"><xsl:value-of select="." /></MPH_MBA_PhD>
</xsl:template>
<xsl:template match="additional_trng" >
  <additional_trng label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></additional_trng>
</xsl:template>
<xsl:template match="req_add_trng" >
  <req_add_trng label="{@label}" ampfile="{@ampfile}"><xsl:value-of select="." /></req_add_trng>
</xsl:template>
<xsl:template match="add_trng_length" >
  <add_trng_length label="{@label}"><xsl:value-of select="." /></add_trng_length>
</xsl:template>

<xsl:template match="primary" >
  <primary label="{@label}"><xsl:value-of select="." /></primary>
</xsl:template>
<xsl:template match="rural" >
  <rural label="{@label}"><xsl:value-of select="." /></rural>
</xsl:template>
<xsl:template match="women" >
  <women label="{@label}"><xsl:value-of select="." /></women>
</xsl:template>
<xsl:template match="hospitalist" >
  <hospitalist label="{@label}"><xsl:value-of select="." /></hospitalist>
</xsl:template>
<xsl:template match="research_trck" >
  <research_trck label="{@label}"><xsl:value-of select="." /></research_trck>
</xsl:template>
<xsl:template match="other" >
  <other label="{@label}"><xsl:value-of select="." /></other>
</xsl:template>
<xsl:template match="exam_req" >
  <exam_req label="{@label}"><xsl:value-of select="." /></exam_req>
</xsl:template>
<xsl:template match="surveys" >
  <surveys label="{@label}"><xsl:value-of select="." /></surveys>
</xsl:template>
<xsl:template match="portfolio" >
  <portfolio label="{@label}"><xsl:value-of select="." /></portfolio>
</xsl:template>
<xsl:template match="degree" >
  <degree label="{@label}"><xsl:value-of select="." /></degree>
</xsl:template>
<xsl:template match="osce" >
  <osce label="{@label}"><xsl:value-of select="." /></osce>
</xsl:template>


<xsl:template match="address1" >
  <address1><xsl:value-of select="." /></address1>
</xsl:template>

<xsl:template match="address2" >
  <address2><xsl:value-of select="." /></address2>
</xsl:template>

<xsl:template match="address3" >
  <address3><xsl:value-of select="." /></address3>
</xsl:template>

<xsl:template match="dir_city" >
  <dir_city><xsl:value-of select="." /></dir_city>
</xsl:template>

<xsl:template match="dir_state" >
  <dir_state><xsl:value-of select="." /></dir_state>
</xsl:template>

<xsl:template match="dir_zip" >
  <dir_zip><xsl:value-of select="." /></dir_zip>
</xsl:template>


</xsl:stylesheet>
