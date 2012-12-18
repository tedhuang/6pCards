<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>{block name="pageTitle"}{/block}</title>
<link type="text/css" href="{$minify['css']|escape}" rel="stylesheet" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="description" http-equiv="description" content="{block name='desription'}{/block}{block name='description'}{/block}" />
</head>

<body>

<div id="container">
  <div id="header">
    {block name="header"}
      <h1>Warning</h1>
    {/block}
  </div>

  <div id="content">
    {block name="content"}
      <h3>This is the default info page for desktop users to find out about your site.  Please extend this template in your theme to provide site-specific details.</h3>
      <p>You probably want to go <a href="../{$homeModuleID}">to the home screen</a></p>
    {/block}
  </div>

  <div id="footer">
    {block name="footer"}{/block}
  </div>

</div><!--/container-->

<script type="text/javascript">
  {block name="footerJavascript"}{/block}
</script>

</body>
</html>
