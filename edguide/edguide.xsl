<?xml version='1.0' encoding='ISO-8859-1'?>

<!-- Version 0.9 - Manuel Canales Esparcia <macana@lfs-es.org>
Based on the original lfs-chunked.xsl created by Matthew Burgess -->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns="http://www.w3.org/1999/xhtml"
                version="1.0">

  	<!-- We use XHTML -->
  <xsl:import href="http://docbook.sourceforge.net/release/xsl/1.65.1/xhtml/chunk.xsl"/>
  <xsl:param name="chunker.output.encoding" select="'ISO-8859-1'"/>
  <xsl:param name="chunk.section.depth" select="0"/>
  
  	<!-- Including our others customized templates -->
  <!-- <xsl:include href="xhtml/lfs-admon.xsl"/>
  <xsl:include href="xhtml/lfs-index.xsl"/>
  <xsl:include href="xhtml/lfs-legalnotice.xsl"/>
  <xsl:include href="xhtml/lfs-mixed.xsl"/> 
  <xsl:include href="xhtml/lfs-navigational.xsl"/>
  <xsl:include href="xhtml/lfs-titles.xsl"/>
  <xsl:include href="xhtml/lfs-toc.xsl"/> -->

  	<!-- The CSS Stylesheet -->
  <xsl:param name="html.stylesheet" select="'edguide.css'"/>

  	<!-- Dropping some unwanted style attributes -->
  <xsl:param name="ulink.target" select="''"></xsl:param>
  <xsl:param name="css.decoration" select="0"></xsl:param>
  
    <!-- No XML declaration -->
  <xsl:param name="chunker.output.omit-xml-declaration" select="'yes'"/>
  
    <!-- Insert a stylesheet for printing -->
  <xsl:template name='user.head.content'>
     <link rel='stylesheet' href="edguide-print.css" type="text/css" media='print'/>
  </xsl:template> 

  <xsl:template match="userinput">
    <xsl:call-template name="inline.monoseq"/>
  </xsl:template>

  <xsl:template match="prefaceinfo|chapterinfo">
    <p class='updated'> Last updated by 
      <xsl:apply-templates select="othername"/>
      on
      <xsl:apply-templates select="date"/> 
    </p>
  </xsl:template>

  <xsl:template name='othername' match="othername"> 
     <xsl:variable name='author'>
          <xsl:value-of select='.'/>
     </xsl:variable>
     <xsl:variable name='nameonly'>
          <xsl:value-of select='substring($author,10)'/>
     </xsl:variable>

     <xsl:value-of select="substring-before($nameonly,'$')" />
  </xsl:template> 

  <xsl:template name='date' match="date"> 
      <xsl:variable name='date'>
         <xsl:value-of select='.'/>
      </xsl:variable>
  
      <xsl:value-of select="substring($date,7,26)" />
  </xsl:template> 

</xsl:stylesheet>
