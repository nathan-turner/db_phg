<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" omit-xml-declaration="yes" /> 


<xsl:template match="/" >

  <xsl:apply-templates select="//program">
    <xsl:sort select="state" />
  </xsl:apply-templates>

  </xsl:template>

<xsl:template match="program" >
<program>
  <xsl:apply-templates select="state" />
  <xsl:apply-templates select="city" />
  <xsl:apply-templates select="state_abbrev" />
  <xsl:apply-templates select="sponsor" />
  <xsl:apply-templates select="sponsor_id" />
  <xsl:apply-templates select="specialty_id" />
  <xsl:apply-templates select="spec_ltr_code" />
  <xsl:apply-templates select="prog_id" />
</program>
</xsl:template>

<xsl:template match="state" >
  <state><xsl:value-of select="." /></state>
</xsl:template>

<xsl:template match="city" >
  <city><xsl:value-of select="." /></city>
</xsl:template>

<xsl:template match="state_abbrev" >
  <state_abbrev><xsl:value-of select="." /></state_abbrev>
</xsl:template>

<xsl:template match="sponsor" >
  <sponsor><xsl:value-of select="." /></sponsor>
</xsl:template>

<xsl:template match="sponsor_id" >
  <sponsor_id><xsl:value-of select="." /></sponsor_id>
</xsl:template>

<xsl:template match="specialty_id" >
  <specialty_id><xsl:value-of select="." /></specialty_id>
</xsl:template>

<xsl:template match="spec_ltr_code" >
  <spec_ltr_code><xsl:value-of select="." /></spec_ltr_code>
</xsl:template>

<xsl:template match="prog_id" >
  <prog_id><xsl:value-of select="." /></prog_id>
</xsl:template>

</xsl:stylesheet>
