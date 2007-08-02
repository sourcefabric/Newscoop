<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:text="http://openoffice.org/2000/text" version="1.0" >

<xsl:template match="/">
	<article>
		<xsl:apply-templates/>
	</article>
</xsl:template>

<xsl:template match="text:user-field-get[@text:name='Author']">
	<author><xsl:value-of select="."/></author>
</xsl:template>

<xsl:template match="text:user-field-input[@text:name='Author']">
	<author><xsl:value-of select="."/></author>
</xsl:template>

<xsl:template match="text:user-field-input[@text:name='Title']">
	<title><xsl:value-of select="."/></title>
</xsl:template>

<xsl:template match="text:user-field-get[@text:name='Title']">
	<title><xsl:value-of select="."/></title>
</xsl:template>

<xsl:template match="text:section[@text:name='Intro']">
	<abstract><xsl:value-of select="."/></abstract>
</xsl:template>

<xsl:template match="text:section[@text:name='Body']">
	<simplesect><xsl:value-of select="."/></simplesect>
</xsl:template>

<xsl:template match="text()"/>

</xsl:stylesheet>

