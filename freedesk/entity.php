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
 * Entity Interface - Search, Edit, Create
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

if (!isset($_REQUEST['mode']))
	$mode="";
else
	$mode=$_REQUEST['mode'];
if (!isset($_REQUEST['entity']))
	$entity="";
else
	$entity=$_REQUEST['entity'];

$table = $DESK->DataDictionary->GetTable($entity);

if ( ($table === false) || (!$table->editable) )
{
	echo "<h3>".$DESK->Lang->Get("entity_not_found")."</h3>";
}
else if (!$DESK->ContextManager->Permission("entity_".$entity))
{
	echo "<h3>".$DESK->Lang->Get("permission_denied")."</h3>";
}
else if ($mode == "search")
{
	echo "<script type=\"text/javascript\">\n";
	echo "DESKSearch.entity = \"".$entity."\";\n";
	echo "</script>\n";
	echo "<div id=\"searchfields\">\n";
	echo "<table class=\"search\">\n";
	echo "<form id=\"entitysearch\" onsubmit=\"return false;\">\n";
	
	$searchnow=false;
	foreach($table->fields as $id => $field)
	{
		if ($field->searchable)
		{
			echo "<tr><td>".$field->name."</td>\n";
			$val="";
			if (isset($_REQUEST[$field->field]))
			{
				$val=$_REQUEST[$field->field];
				if ($val!="")
					$searchnow=true;
			}
			echo "<td><input type=\"text\" name=\"".$field->field."\" value=\"".$val."\"></td></tr>\n";
		}
	}
	echo "<tr><td>&nbsp;</td>\n";
	echo "<td><input type=\"submit\" value=\"".$DESK->Lang->Get("search")."\" onclick=\"DESKSearch.search();\"></td>\n";
	echo "</tr>";
	echo "</form></table>\n";
	echo "</div>\n";
	
	echo "<div id=\"searchresults\">\n";
	echo "</div>";
	
	
	echo "<script type=\"text/javascript\">\n";
	if (isset($_REQUEST['callback']) && $_REQUEST['callback']!="")
		echo "DESKSearch.callback = window.opener.".$_REQUEST['callback'].";\n";
	else
		echo "DESKSearch.callback = null;\n";
	if (isset($_REQUEST['onereturn']) && $_REQUEST['onereturn']==1)
		echo "DESKSearch.callbackOnSingle = true;\n";
	else
		echo "DESKSearch.callbackOnSingle = false;\n";
	if ($searchnow)
		echo "DESKSearch.search();\n";
	echo "</script>\n";
	
}
else if ($mode == "edit")
{
	$loaded = $DESK->EntityManager->Load($entity, $_REQUEST['value']);
	if ($loaded !== false)
	{
		$data = $loaded->GetData();
		echo "<table class=\"edit\">\n";
		echo "<form id=\"entity_edit\" onsubmit=\"return false;\">\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"entity_save\">\n";
		echo "<input type=\"hidden\" name=\"entity\" value=\"".$entity."\">\n";
		foreach($table->fields as $id => $field)
		{
			echo "<tr>\n";
			echo "<td>".$field->name."</td>\n";
			echo "<td>\n";
			// TODO: Different field types in full
			if ($field->type == DD_FieldType::Password)
				$t="password";
			else
				$t="text";
			$i="<input type=\"".$t."\" name=\"".$id."\" value=\"".$data[$id]."\"";
			if ($field->readonly)
				$i.=" readonly";
			$i.=">";
			echo $i;
			echo "</td>\n";
			echo "</tr>\n";
		}
		
		echo "<tr><td>\n";
		echo "<a href=\"#\" onclick=\"window.close();\">Cancel</a>";
		echo "</td><td>";
		echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('entity_edit');\"> ";
		echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save_close")."\" onclick=\"DESK.formAPI('entity_edit',true);\">";
		echo "</td></tr>\n";
		
		echo "</form></table>\n";
	}
	else
	{
		echo "Entity Load Failed";
	}
}
else if ($mode == "create")
{
	echo "<table class=\"create\">\n";
	echo "<form id=\"entity_create\" onsubmit=\"return false;\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"entity_create\">\n";
	echo "<input type=\"hidden\" name=\"entity\" value=\"".$entity."\">\n";
	foreach($table->fields as $id => $field)
	{
		echo "<tr>\n";
		echo "<td>".$field->name."</td>\n";
		echo "<td>\n";
		// TODO: Different field types in full
		if ($field->type == DD_FieldType::Password)
				$t="password";
			else
				$t="text";
		echo "<input type=\"".$t."\" name=\"".$id."\" value=\"\">\n";
		echo "</td></tr>";
	}
	echo "<tr><td>\n";
	echo "<a href=\"#\" onclick=\"window.close();\">Cancel</a>";
	echo "</td><td>";
	echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('entity_create');\"> ";
	echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save_close")."\" onclick=\"DESK.formAPI('entity_create',true);\">";
	echo "</td></tr>\n";
	
	echo "</form></table>\n";
}

else
{
	echo "<h3>".$DESK->Lang->Get("action_invalid")."</h3>";
}
			

$DESK->Skin->IncludeFile("min_footer.php");


?>
