<?xml version="1.0"?>
<xsl:stylesheet version="1.0" 
       xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 
<xsl:output method="xml" omit-xml-declaration="yes" /> 


<xsl:template match="/" >
<listings>
  <xsl:apply-templates select="//program">
    <xsl:sort select="city" />
  </xsl:apply-templates>
</listings>
  </xsl:template>

<xsl:template match="program" >
<program>
  <xsl:apply-templates select="state" />
  <xsl:apply-templates select="city" />
  <xsl:apply-templates select="positions" />
  <xsl:apply-templates select="salary" />
  <xsl:apply-templates select="prog_name" />
  <xsl:apply-templates select="prog_id" />
  <xsl:apply-templates select="specialty_id" />
  <xsl:apply-templates select="spec_ltr_code" />
</program>
</xsl:template>

<xsl:template match="state" >
  <state><xsl:value-of select="." /></state>
</xsl:template>

<xsl:template match="city" >
  <city><xsl:value-of select="." /></city>
</xsl:template>

<xsl:template match="positions" >
  <positions><xsl:value-of select="." /></positions>
</xsl:template>

<xsl:template match="salary" >
  <salary><xsl:value-of select="." /></salary>
</xsl:template>

<xsl:template match="prog_name" >
  <prog_name><xsl:value-of select="." /></prog_name>
</xsl:template>

<xsl:template match="prog_id" >
  <prog_id><xsl:value-of select="." /></prog_id>
</xsl:template>

<xsl:template match="specialty_id" >
  <specialty_id><xsl:value-of select="." /></specialty_id>
</xsl:template>

<xsl:template match="spec_ltr_code" >
  <spec_ltr_code><xsl:value-of select="." /></spec_ltr_code>
</xsl:template>

</xsl:stylesheet>
