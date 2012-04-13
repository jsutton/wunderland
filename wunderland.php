<?php
//////////////////////////////////////////////////////////////////////////////
//
// wunderland.php
//
// Original Attribution
// PHP Looking Glass - Copyright (c) 2000
// version 1.8 (2009)
// by
// Gabriella Paolini - gabriella.paolini@garr.it
// Updated by Jered Sutton - jered.sutton@gmail.com
// Looking Glass for CISCO Routers.
//
//////////////////////////////////////////////////////////////////////////////
// Default Password
$pw = "****\r\n";

//telnet information
$cfgPort    = 23;
$cfgTimeOut = 10;

//define the cisco devices here
$deviceList = array(
'Kansas' => array(
  'ip' =>'192.168.1.1',
  'sourceip' =>'192.168.2.1',
  'pw' => '****\r\n',
  ),
'New York' => array(
  'ip' =>'172.16.10.1',
  'sourceip' =>'172.16.20.1',
  'pw' => '****\r\n',
  ),
'LA' => array(
  'ip' =>'10.1.1.1',
  'sourceip' =>'',
  'pw' => '****\r\n',
  )
);

?>
<HTML>
<HEAD>
<title>Wunderland Looking Glass</title>
</HEAD>
<style type="text/css" media="all">@import url(style.css);</style>
<div id='container'>
<BODY>
<h1>Wunderland Looking Glass</h1>

<FORM ACTION="index.php" METHOD="POST">
<TABLE CELLPADDING="2" cellspacing="2">

<tr>
<TD>Device:</TD>
<TD>Command:</TD>
<TD>IP Address:</TD>
<td></td>
</tr>
<tr>
<TD>

<SELECT NAME="device">
<?php
foreach (array_keys($deviceList) as $k) {
  echo "<option value=\"$k\">$k</option>";
}
php?>
</SELECT>

</TD>
    <TD>
        <SELECT NAME="query">
            <OPTION VALUE="traceroute" > traceroute
            <OPTION VALUE="ping" > ping
        </SELECT>

</td>
<TD><input type="text" name="destip" size="25"></TD>
<td><INPUT TYPE=submit VALUE="Submit">&nbsp;&nbsp; <INPUT TYPE=reset VALUE="Reset"></td>
</tr>
</TABLE>
</FORM>
</div>
<div align="left">
<pre>
<?php

//retrieve POST values
$device = $_POST['device'];
$query = $_POST['query'];
$destip = $_POST['destip'];
$ip = $deviceList[$device]['ip'];

// validate all input for the script
if ( ($ip != "") &&
        ($query == "ping" || $query == "traceroute") &&
        preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" . "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $destip) &&
        getenv("REQUEST_METHOD") != "post"){

        //open the telnet connection
        $usenet = fsockopen($ip, $cfgPort, $errno, $errstr, $cfgTimeOut);
        socket_set_timeout($usenet, 3);

        if(!$usenet)
                echo "Cannot connect to the router \n";
        //construct the command string
                else {
                if ($query == "traceroute")
            if($sourceip != "")
                                $command = $query." ip ".$destip." source ".$sourceip."\r\n";
            else
                                $command = $query." ".$destip."\r\n";
                                        elseif ($query == "ping")
                        $command = $query." ".$destip."\r\n";
            if($sourceip != "")
                                $command = $query." ip ".$destip." source ".$sourceip."\r\n";
            else
                                $command = $query." ".$destip."\r\n";
                //send password and text terminal setting
        fputs ($usenet, $pw);
        fputs ($usenet, "terminal length 0\r\n");
        fputs ($usenet, "\r\n");
        //absorb extraneous terminal output
                $j = 0;
                while ($j<7){
          fgets($usenet, 128);
           $j++;
        }

                //run the command
        fputs ($usenet, $command);
                //some formatting
                echo "<hr>";
                //print the results
                while ($r = fgets($usenet, 128)) {
                        echo "$r";
                        //enables realtime output to the browser
                        ob_flush();
                        flush();
        }
                echo "<hr>";
                //logout
        fputs ($usenet, "exit\r\n");
                //close the fsocket
        fclose($usenet);
        }
}
?>

</PRE>
</div>
<br>
<br>
<p align="center">Wunderland Looking Glass v 1.8 - php
script made by <a
href="mailto:gabriella.paolini@garr.it">Gabriella Paolini</a>  - Open
Source 2000-2002 Modified by Jered Sutton</p>
</body>