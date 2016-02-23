function makesetupxsl(sortby){
var sortalias = "999999"
//// ALL NON-NUMBER, NON-YES/NO FIELDS /// 
if(sortby=="state"||sortby=="city"||sortby=="spec_ltr_code"||sortby=="prog_name"||sortby=="setting"||sortby=="most_taxing"||sortby=="duration"||sortby=="multi_start"||sortby=="research"||sortby=="subspec"){
  sortalias = "ZZZ"
}
if(sortby=="FREIDA" || extrasort=="FREIDA"){
  sortalias = "No"
}
if(sortby=="salary" || extrasort=="salary"){
  sortalias = "0"
}
//alert("sortby: " + sortby + "\nextrasort: " + extrasort);

var xslbody = "<?xml version=\"1.0\"?>\n"
xslbody += "<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\n"
xslbody += "<xsl:output method=\"xml\" omit-xml-declaration=\"yes\" />\n"
xslbody += "<xsl:template match=\"/\" >\n"
xslbody += "  <xsl:apply-templates select=\"//program\">\n"
xslbody += "    <xsl:sort select=\""+sortby+"\" />\n"
xslbody += "  </xsl:apply-templates>\n"
xslbody += "  </xsl:template>\n"
xslbody += "<xsl:template match=\"program\" >\n"
xslbody += "<program>\n"
xslbody += "  <xsl:apply-templates select=\"state\" />\n"
xslbody += "  <xsl:apply-templates select=\"city\" />\n"
xslbody += "  <xsl:apply-templates select=\"prog_name\" />\n"
xslbody += "  <xsl:apply-templates select=\"Prevmedarea\" />\n"
xslbody += "  <xsl:apply-templates select=\"prog_id\" />\n"
xslbody += "  <xsl:apply-templates select=\"specialty_id\" />\n"
xslbody += "  <xsl:apply-templates select=\"spec_ltr_code\" />\n"
if((extrasort!="")){
  xslbody += "  <xsl:apply-templates select=\""+extrasort+"\" />\n"
}
xslbody += "</program>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"state\" >\n"
xslbody += "  <state><xsl:value-of select=\".\" /></state>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"city\" >\n"
xslbody += "  <city><xsl:value-of select=\".\" /></city>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"prog_name\" >\n"
xslbody += "  <prog_name><xsl:value-of select=\".\" /></prog_name>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"Prevmedarea\" >\n"
xslbody += "  <Prevmedarea><xsl:value-of select=\".\" /></Prevmedarea>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"prog_id\" >\n"
xslbody += "  <prog_id><xsl:value-of select=\".\" /></prog_id>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"specialty_id\" >\n"
xslbody += "  <specialty_id><xsl:value-of select=\".\" /></specialty_id>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"spec_ltr_code\" >\n"
xslbody += "  <spec_ltr_code><xsl:value-of select=\".\" /></spec_ltr_code>\n"
xslbody += "</xsl:template>\n"

if(extrasort!=""){
  xslbody += "<xsl:template match=\""+extrasort+"\" >\n"
  xslbody += "<xsl:variable name=\"strextrasort\" >\n"
  xslbody += "  <xsl:value-of select=\".\" />\n"
  xslbody += "</xsl:variable>\n"
    xslbody += "<xsl:choose>\n"
    xslbody += "  <xsl:when test=\"$strextrasort=''\">\n"
    xslbody += "    <"+extrasort+">"+ sortalias + "</"+extrasort+">\n"
    xslbody += "  </xsl:when>\n"
  if(sortby!="FREIDA" && extrasort!="FREIDA"){
    xslbody += "  <xsl:otherwise>\n"
    xslbody += "    <"+extrasort+"><xsl:value-of select=\"$strextrasort\" /></"+extrasort+">\n"
    xslbody += "  </xsl:otherwise>\n"
  }else{
    xslbody += "  <xsl:otherwise>\n"
    xslbody += "    <"+extrasort+">Yes</"+extrasort+">\n"
    xslbody += "  </xsl:otherwise>\n"
  }
  xslbody += "</xsl:choose>\n"
  xslbody += "</xsl:template>\n"
}
xslbody += "</xsl:stylesheet>\n"

//alert(xslbody)
return xslbody
}





function makesortxsl(sortby){
var sortorder = "ascending"
//// ALL YES/NO FIELDS AND PROGRAM SIZE (Fred wants that to sort descending) ///
if(sortby=="positions"||sortby=="salary"||sortby=="FREIDA"||sortby=="moonlighting"||sortby=="child"||sortby=="pt_shared"||sortby=="qual_imp"||sortby=="international"||sortby=="retreats"||sortby=="offcampus"||sortby=="hospice"||sortby=="cultcomp"||sortby=="nonenglish"||sortby=="alt_comp"||sortby=="MPH_MBA"||sortby=="PhD"||sortby=="additional_trng"||sortby=="req_add_trng"||sortby=="military"||sortby=="freida"){
  sortorder = "descending"
}
var xslbody = "<?xml version=\"1.0\"?>\n"
xslbody += "<xsl:stylesheet version=\"1.0\"\n"
xslbody += "       xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\n"
xslbody += "<xsl:output method=\"xml\" omit-xml-declaration=\"yes\" /> \n"
xslbody += "<xsl:template match=\"/\" >\n"
xslbody += "<listings>\n"

xslbody += "  <xsl:apply-templates select=\"//program\">\n"
  xslbody += "<xsl:sort select=\"" + sortby + "\" order=\"" + sortorder + "\" data-type=\"" + datatype + "\" />\n"
xslbody += "  </xsl:apply-templates>\n"
xslbody += "</listings>\n"
xslbody += "  </xsl:template>\n"
xslbody += "<xsl:template match=\"program\" >\n"
xslbody += "<program>\n"
xslbody += "  <xsl:apply-templates select=\"state\" />\n"
xslbody += "  <xsl:apply-templates select=\"city\" />\n"
xslbody += "  <xsl:apply-templates select=\"prog_name\" />\n"
xslbody += "  <xsl:apply-templates select=\"Prevmedarea\" />\n"
xslbody += "  <xsl:apply-templates select=\"prog_id\" />\n"
xslbody += "  <xsl:apply-templates select=\"specialty_id\" />\n"
xslbody += "  <xsl:apply-templates select=\"spec_ltr_code\" />\n"
if((extrasort!="")){
  xslbody += "  <xsl:apply-templates select=\""+extrasort+"\" />\n"
}
xslbody += "</program>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"state\" >\n"
xslbody += "  <state><xsl:value-of select=\".\" /></state>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"city\" >\n"
xslbody += "  <city><xsl:value-of select=\".\" /></city>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"prog_name\" >\n"
xslbody += "  <prog_name><xsl:value-of select=\".\" /></prog_name>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"Prevmedarea\" >\n"
xslbody += "  <Prevmedarea><xsl:value-of select=\".\" /></Prevmedarea>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"prog_id\" >\n"
xslbody += "  <prog_id><xsl:value-of select=\".\" /></prog_id>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"specialty_id\" >\n"
xslbody += "  <specialty_id><xsl:value-of select=\".\" /></specialty_id>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"spec_ltr_code\" >\n"
xslbody += "  <spec_ltr_code><xsl:value-of select=\".\" /></spec_ltr_code>\n"
xslbody += "</xsl:template>\n"
if(extrasort!=""){
  xslbody += "<xsl:template match=\""+extrasort+"\" >\n"
  xslbody += "<xsl:variable name=\"strextrasort\" >\n"
  xslbody += "  <xsl:value-of select=\".\" />\n"
  xslbody += "</xsl:variable>\n"
  xslbody += "<xsl:choose>\n"
  xslbody += "  <xsl:when test=\"$strextrasort='ZZZ'\">\n"
  xslbody += "    <"+extrasort+">_</"+extrasort+">\n"
  xslbody += "  </xsl:when>\n"
  xslbody += "  <xsl:when test=\"$strextrasort='999999'\">\n"
  xslbody += "    <"+extrasort+">_</"+extrasort+">\n"
  xslbody += "  </xsl:when>\n"
  xslbody += "  <xsl:when test=\"$strextrasort='0'\">\n"
  xslbody += "    <"+extrasort+">_</"+extrasort+">\n"
  xslbody += "  </xsl:when>\n"
  xslbody += "  <xsl:otherwise>\n"
  xslbody += "    <"+extrasort+"><xsl:value-of select=\"$strextrasort\" /></"+extrasort+">\n"
  xslbody += "  </xsl:otherwise>\n"
  xslbody += "</xsl:choose>\n"
  xslbody += "</xsl:template>\n"
}
xslbody += "</xsl:stylesheet>"

//alert(xslbody)
return xslbody
}







function makehtmlxsl(sortby){
var xslbody = "<?xml version=\"1.0\"?>\n"
xslbody += "<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\n"
xslbody += "<xsl:template match=\"/\" >\n"
xslbody += "  <xsl:apply-templates select=\"//program\">\n"
xslbody += "  </xsl:apply-templates>\n"
xslbody += "</xsl:template>\n"
xslbody += "<xsl:template match=\"program\">\n"
xslbody += "<xsl:variable name=\"strprevmedarea\" >\n"
xslbody += "  <xsl:value-of select=\"child::Prevmedarea\" />\n"
xslbody += "</xsl:variable>\n"
xslbody += "<form name=\"{prog_id}\">\n"
xslbody += "<tr>\n"
xslbody += "    <td width=\"10\"><img src=\"images/transpix.gif\" width=\"10\" height=\"1\" alt=\"\" /></td>\n"

if(extrasort!=""){
  if(datatype=="number" && sortby!="FREIDA"){
    xslbody += "<xsl:variable name=\"strextrasort\" >\n"
    xslbody += "  <xsl:value-of select=\"child::" + extrasort + "\" />\n"
    xslbody += "</xsl:variable>\n"
    xslbody += "<xsl:choose>\n"
    xslbody += "  <xsl:when test=\"$strextrasort='_'\">\n"
    xslbody += "  <td class=\"csstextcenter\" nowrap=\"true\" valign=\"top\"><a HREF=\"javascript:top.btnbar.reselect('"+extrasort+"','{"+extrasort+"}');\"><font color=\"#0000dd\"><xsl:value-of select=\"$strextrasort\" /></font></a></td>"
    xslbody += "  </xsl:when>\n"
    xslbody += "  <xsl:otherwise>\n"
    xslbody += "  <td class=\"csstextcenter\" nowrap=\"true\" valign=\"top\"><a HREF=\"javascript:top.btnbar.reselect('"+extrasort+"','{"+extrasort+"}');\"><font color=\"#0000dd\"><xsl:number value=\"$strextrasort\" grouping-separator=\",\" grouping-size=\"3\" /></font></a></td>"
    xslbody += "  </xsl:otherwise>\n"
    xslbody += "</xsl:choose>\n"
  }else{
    xslbody += "<xsl:variable name=\"strextrasort\" >\n"
      xslbody += "  <xsl:value-of select=\"child::" + extrasort + "\" />\n"
    xslbody += "</xsl:variable>\n"
    xslbody += "  <td class=\"csstextcenter\" nowrap=\"true\" valign=\"top\"><a HREF=\"javascript:top.btnbar.reselect('"+extrasort+"','{"+extrasort+"}');\"><font color=\"#0000dd\"><xsl:value-of select=\""+extrasort+"\" /></font></a></td>\n"
  }
  xslbody += "    <td width=\"10\"><img src=\"images/transpix.gif\" width=\"10\" height=\"1\" alt=\"\" /></td>\n"
}
xslbody += "  <td class=\"csstext\" nowrap=\"true\" valign=\"top\"><a HREF=\"javascript:top.btnbar.reselect('state','{state}');\"><font color=\"#0000dd\"><xsl:value-of select=\"state\" /></font></a></td>\n"
xslbody += "    <td width=\"10\"><img src=\"images/transpix.gif\" width=\"10\" height=\"1\" alt=\"\" /></td>\n"
xslbody += "  <td class=\"csstext\" nowrap=\"true\" valign=\"top\"><a HREF=\"javascript:top.btnbar.reselect('city','{city}');\"><font color=\"#0000dd\"><xsl:value-of select=\"city\" /></font></a></td>\n"
xslbody += "    <td width=\"10\"><img src=\"images/transpix.gif\" width=\"10\" height=\"1\" alt=\"\" /></td>\n"
xslbody += "  <td><input type=\"checkbox\" name=\"detailcheckbox\" class=\"csstext\" /></td>\n"
xslbody += "    <td><img src=\"images/transpix.gif\" width=\"10\" height=\"1\" alt=\"\" /></td>\n"
xslbody += "  <td class=\"csstextred\" nowrap=\"true\" valign=\"top\"><xsl:value-of select=\"spec_ltr_code\" /></td>\n"
xslbody += "    <td><img src=\"images/transpix.gif\" width=\"10\" height=\"1\" alt=\"\" /></td>\n"
xslbody += "  <td class=\"csstext\" nowrap=\"true\" valign=\"top\"><xsl:value-of select=\"prog_name\" />\n"
xslbody += "  </td>\n"
xslbody += "</tr>\n"
xslbody += "  </form>\n"
xslbody += "</xsl:template>\n"
xslbody += "</xsl:stylesheet>\n"

//alert(xslbody)
return xslbody
}


