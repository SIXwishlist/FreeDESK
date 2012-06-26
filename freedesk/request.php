<?php 
/* -------------------------------------------------------------
This file is part of FreeDESK

FreeDESK is (C) Copyright 2012 David Cutting

FreeDESK is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FreeDESK is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FreeDESK.  If not, see www.gnu.org/licenses

For more information see www.purplepixie.org/freedesk/
-------------------------------------------------------------- */

/**
 * Request Display
**/


// Output buffer on and start FreeDESK then discard startup whitespace-spam
ob_start();
include("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();
ob_end_clean();


if (!isset($_REQUEST['sid']) || !$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
{
	$data=array("title"=>$DESK->Lang->Get("welcome"));
	$DESK->Skin->IncludeFile("min_header.php",$data);

	echo "\n<noscript>\n";
	echo "<h1>Sorry you must have Javascript enabled to use FreeDESK analyst portal</h1>\n";
	echo "</noscript>\n";

	echo "<h3>".$DESK->Lang->Get("login_invalid").":</h3>\n";

	
	$DESK->Skin->IncludeFile("min_footer.php");
	exit();
}


// So we're authenticated let's view the main page
$data=array("title"=>"FreeDESK");
$DESK->Skin->IncludeFile("min_header.php",$data);

if (isset($_REQUEST['id']))
{
$id=$_REQUEST['id'];

$request = $DESK->RequestManager->Fetch($id);

if ($request === false)
{
	echo $DESK->Lang->Get("entity_not_found");
	$DESK->Skin->IncludeFile("min_footer.php");
	exit();
}

$request->LoadUpdates();

$panes = array(
	"log" => array( "title" => "Request History" ),
	"details" => array( "title" => "Details" ),
	"update" => array( "title" => "Update Request" ) );

$data = array( "id" => "request", "panes" => $panes );
$DESK->Skin->IncludeFile("pane_start.php", $data);

echo "<div id=\"pane_request_log_content\" class=\"pane_content\">\n";

$updates = $request->GetUpdates();

foreach($updates as $update)
{
	echo "<div id=\"update_".$update['updateid']."\" class=\"update\">\n";
	echo "<div id=\"update_header_".$update['updateid']."\" class=\"update_header\">\n";
	echo $update['updatedt']." : ".$update['updateby']."\n";
	echo "</div>\n";
	echo "<div id=\"update_content_".$update['updateid']."\" class=\"update_content\">";
	echo $update['update']."\n\n";
	echo "</div>\n";
	echo "</div>\n";
}

echo "</div>";

echo "<div id=\"pane_request_details_content\" class=\"pane_content_hidden\">\n";
echo "Details";
echo "</div>";

echo "<div id=\"pane_request_update_content\" class=\"pane_content_hidden\">\n";

echo "<form id=\"request_update\" onsubmit=\"return false;\">";
echo "<input type=\"hidden\" name=\"mode\" value=\"request_update\">\n";
echo "<input type=\"hidden\" name=\"requestid\" value=\"".$id."\">\n";
echo "<textarea rows=\"10\" cols=\"50\" name=\"update\"></textarea><br />\n";
echo $DESK->Lang->Get("assign")." ";

echo "<select name=\"assign\">\n";

echo "<option value=\"\" selected>".$DESK->Lang->Get("no_change")."</option>\n";

$list = $DESK->RequestManager->TeamUserList();

foreach($list as $teamid => $team)
{
	$teamname = $team['name'];
	if ($team['assign'])
		echo "<option value=\"".$teamid."\">".$teamname."</option>\n";
	if (is_array($team['items']))
	{
		foreach($team['items'] as $username => $detail)
		{
			if ($team['team'])
				$tid = $teamid;
			else
				$tid = 0;
			if ($detail['assign'])
				echo "<option value=\"".$tid."/".$username."\">".$teamname." &gt; ".$detail['realname']."</option>\n";
		}
	}
}
echo "</select>\n";

echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('request_update',false,true);\">";

echo "</form>\n";

/*
echo "<hr class=\"request\">\n";

echo $DESK->Lang->Get("just_assign")." ";
echo "<form id=\"request_assign\" onsubmit=\"return false;\">\n";
echo "<input type=\"hidden\" name=\"mode\" value=\"request_update\">\n";
echo "<input type=\"hidden\" name=\"requestid\" value=\"".$id."\">\n";
echo "<select name=\"assign\">\n";
foreach($list as $teamid => $team)
{
	$teamname = $team['name'];
	if ($team['assign'])
		echo "<option value=\"".$teamid."\">".$teamname."</option>\n";
	if (is_array($team['items']))
	{
		foreach($team['items'] as $username => $detail)
		{
			if ($team['team'])
				$tid = $teamid;
			else
				$tid = 0;
			if ($detail['assign'])
				echo "<option value=\"".$tid."/".$username."\">".$teamname." &gt; ".$detail['realname']."</option>\n";
		}
	}
}
echo "</select>\n";
echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('request_assign',false,true);\">";
echo "</form>";
*/

echo "</div>";



$DESK->Skin->IncludeFile("pane_finish.php");
}
else // new request
{
//echo "<b>".$DESK->Lang->Get("customer")."</b>\n";

echo "<div id=\"customer_select\" class=\"customer_select\">\n";

echo "<table class=\"search\">\n";
echo "<form id=\"customersearch\" onsubmit=\"return false;\">\n";

$table=$DESK->DataDictionary->Tables["customer"];

foreach($table->fields as $id => $field)
{
	if ($field->searchable)
	{
		echo "<tr><td>".$field->name."</td>\n";
		$val="";
		if (isset($_REQUEST[$field->field]))
		{
			$val=$_REQUEST[$field->field];
			$searchnow=true;
		}
		echo "<td><input type=\"text\" name=\"".$field->field."\" value=\"".$val."\"></td></tr>\n";
	}
}
echo "<tr><td>&nbsp;</td>\n";
echo "<td><input type=\"submit\" value=\"".$DESK->Lang->Get("search")."\" onclick=\"DESKRequest.searchCustomer();\"></td>\n";
echo "</tr>";
echo "</form></table>\n";


echo "</div>";
echo "<div id=\"customer_details\" class=\"customer_details_hidden\">\n";
echo "</div>";

echo "<hr class=\"request\" />\n";
}



$DESK->Skin->IncludeFile("min_footer.php");


?>
