<?xml version='1.0' encoding='ISO-8859-1'?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns="http://www.w3.org/1999/xhtml"
                version="1.0">

    <!-- screen -->
  <xsl:template match="screen">
    <xsl:choose>
      <xsl:when test="child::* = userinput">
        <pre class="userinput">
            <xsl:apply-templates/>
        </pre>
      </xsl:when>
      <xsl:otherwise>
        <pre class="{name(.)}">
          <xsl:apply-templates/>
        </pre>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
    <!-- userinput -->
  <xsl:template match="userinput">
    <xsl:choose>
      <xsl:when test="ancestor::screen">
        <kbd class="command">
          <xsl:apply-templates/>
        </kbd>
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-imports/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

    <!-- Body attributes -->
  <xsl:template name="body.attributes">
    <xsl:attribute name="id">
      <xsl:text>blfs</xsl:text>
    </xsl:attribute>
    <xsl:attribute name="class">
      <xsl:value-of select="substring-after(/book/bookinfo/subtitle, ' ')"/>
    </xsl:attribute>
  </xsl:template>

    <!-- External URLs in italic font -->
  <xsl:template match="ulink" name="ulink">
    <a>
      <xsl:if test="@id">
        <xsl:attribute name="id">
          <xsl:value-of select="@id"/>
        </xsl:attribute>
      </xsl:if>
      <xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
       <strong>
        <xsl:choose>
          <xsl:when test="count(child::node())=0">
            <xsl:value-of select="@url"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:apply-templates/>
          </xsl:otherwise>
        </xsl:choose>
      </strong>
    </a>
  </xsl:template>
  
    <!-- The <code> xhtml tag have look issues in some browsers, like Konqueror and.
      isn't semantically correct (a filename isn't a code fragment) We will use <tt> for now. -->
  <xsl:template name="inline.monoseq">
    <xsl:param name="content">
      <xsl:call-template name="anchor"/>
      <xsl:call-template name="simple.xlink">
        <xsl:with-param name="content">
          <xsl:apply-templates/>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:param>
    <tt class="{local-name(.)}">
      <xsl:if test="@dir">
        <xsl:attribute name="dir">
          <xsl:value-of select="@dir"/>
        </xsl:attribute>
      </xsl:if>
      <xsl:copy-of select="$content"/>
    </tt>
  </xsl:template>
  
  <xsl:template name="inline.boldmonoseq">
    <xsl:param name="content">
      <xsl:call-template name="anchor"/>
      <xsl:call-template name="simple.xlink">
        <xsl:with-param name="content">
          <xsl:apply-templates/>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:param>
    <!-- don't put <strong> inside figure, example, or table titles -->
    <!-- or other titles that may already be represented with <strong>'s. -->
    <xsl:choose>
      <xsl:when test="local-name(..) = 'title' and (local-name(../..) = 'figure' 
              or local-name(../..) = 'example' or local-name(../..) = 'table' or local-name(../..) = 'formalpara')">
        <tt class="{local-name(.)}">
          <xsl:if test="@dir">
            <xsl:attribute name="dir">
              <xsl:value-of select="@dir"/>
            </xsl:attribute>
          </xsl:if>
          <xsl:copy-of select="$content"/>
        </tt>
      </xsl:when>
      <xsl:otherwise>
        <strong class="{local-name(.)}">
          <tt>
            <xsl:if test="@dir">
              <xsl:attribute name="dir">
                <xsl:value-of select="@dir"/>
              </xsl:attribute>
            </xsl:if>
            <xsl:copy-of select="$content"/>
          </tt>
        </strong>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template name="inline.italicmonoseq">
    <xsl:param name="content">
      <xsl:call-template name="anchor"/>
      <xsl:call-template name="simple.xlink">
        <xsl:with-param name="content">
          <xsl:apply-templates/>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:param>
    <em class="{local-name(.)}">
      <tt>
        <xsl:if test="@dir">
          <xsl:attribute name="dir">
            <xsl:value-of select="@dir"/>
          </xsl:attribute>
        </xsl:if>
        <xsl:copy-of select="$content"/>
      </tt>
    </em>
  </xsl:template>

</xsl:stylesheet>
