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
 * Permission Manager
**/
class PermissionManager
{
	/**
	 * FreeDESK Instance
	**/
	private $DESK = null;
	
	/**
	 * User Permissions Loaded
	**/
	private $userperm = array();
	
	/**
	 * Group Permissions Loaded
	**/
	private $groupperm = array();
	
	/**
	 * Permission Sets and Defaults
	**/
	private $permissions = array();
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK FreeDESK instance
	**/
	function PermissionManager(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
	}
	
	/**
	 * Register a Permission Attribute
	 * @param string $permission Permission tag
	 * @param bool $default Default response (optional, default false)
	**/
	function Register($permission, $default=false)
	{
		$this->permissions[$permission]=$default;
		//$this->DESK->LoggingEngine->Log("Reg: ".$permission,"Ha","Ha");
	}
	
	/**
	 * Check if a permission tag exists
	 * @param string $permission Permission tag
	 * @return bool True if exists, false on failure
	**/
	function PermissionExists($permission)
	{
		if (isset($this->permissions[$permission]))
			return true;
		return false;
	}
	
	/**
	 * Check a user permission
	 * @param string $permission Permission tag
	 * @param string $username Username
	 * @return bool true if allowed or false if denied
	**/
	function UserPermission($permission, $username)
	{
		if (!isset($this->userperm[$username]))
			$this->LoadUser($username);
		
		if (isset($this->userperm[$username][$permission]))
			return $this->userperm[$username][$permission];
		if (isset($this->userperm[$username]['default']))
			return $this->userperm[$username]['default'];
		
		// Otherwise we try for a group
		$q="SELECT ".$this->DESK->Database->Field("permgroup")." FROM ".$this->DESK->Database->Table("user");
		$q.=" WHERE ".$this->DESK->Database->Field("username")."=\"".$this->DESK->Database->Safe($username)."\" LIMIT 0,1";
		$r=$this->DESK->Database->Query($q);
		if ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$this->DESK->Database->Free($r);
			$group = $row['permgroup'];
			if ($group != 0)
			{
				if (!isset($this->groupperm[$group]))
					$this->LoadGroup($group);
				if (isset($this->groupperm[$group][$permission]))
					return $this->groupperm[$group][$permission];
				if (isset($this->groupperm[$group]['default']))
					return $this->groupperm[$group]['default'];
			}
		}
		
		// No user or group preference so check for a code default
		if (isset($this->permissions[$permission]))
			return $this->permissions[$permission];
		
		// Nothing set for this permission so deny
		return false;
	}
	
	/**
	 * Load a users permissions
	 * @param string $username Username
	**/
	private function LoadUser($username)
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("permissions")." WHERE ";
		$q.=$this->DESK->Database->Field("permissiontype")."=\"user\" AND ";
		$q.=$this->DESK->Database->Field("usergroupid")."=\"".$this->DESK->Database->Safe($username)."\"";
		
		$r=$this->DESK->Database->Query($q);
		
		$this->userperm[$username] = array();
		
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			if ($row['allowed']==1)
				$this->userperm[$username][$row['permission']] = true;
			else
				$this->userperm[$username][$row['permission']] = false;
		}
		
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Load a groups permissions
	 * @param int $permgroupid Permission Group ID
	**/
	private function LoadGroup($permgroupid)
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("permissions")." WHERE ";
		$q.=$this->DESK->Database->Field("permissiontype")."=\"group\" AND ";
		$q.=$this->DESK->Database->Field("usergroupid")."=\"".$this->DESK->Database->Safe($permgroupid)."\"";
		
		$r=$this->DESK->Database->Query($q);
		
		$this->groupperm[$permgroupid] = array();
		
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			if ($row['allowed']==1)
				$this->groupperm[$permgroupid][$row['permission']] = true;
			else
				$this->groupperm[$permgroupid][$row['permission']] = false;
		}
		
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Get the full set of permissions
	 * @return array Permission list
	**/
	function PermissionList()
	{
		$permlist = $this->permissions;
		if (!isset($permlist['default']))
			$permlist['default']=false;
		return $permlist;
	}
	
	/**
	 * Get user settings (user-specific, not group or anything else) for permissions
	 * @param string $username Username
	 * @return array Array of permissions with form (-1 undefined, 0 denied, 1 allowed)
	**/
	function UserPermissionList($username)
	{
		$permlist = $this->PermissionList();
		
		$perms = array("default" => -1);
		
		foreach($permlist as $key => $perm)
			$perms[$key]=-1;
		
		
		$q="SELECT ".$this->DESK->Database->Field("permission").",".$this->DESK->Database->Field("allowed")." ";
		$q.="FROM ".$this->DESK->Database->Table("permissions")." WHERE ";
		$q.=$this->DESK->Database->Field("permissiontype")."=".$this->DESK->Database->SafeQuote("user")." AND ";
		$q.=$this->DESK->Database->Field("usergroupid")."=".$this->DESK->Database->SafeQuote($username);
		
		$r=$this->DESK->Database->Query($q);
		
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$perms[$row['permission']] = $row['allowed'];
		}
		
		$this->DESK->Database->Free($r);
		
		return $perms;
	}
	
	/**
	 * Get group settings for permissions
	 * @param string $groupid Group ID
	 * @return array Array of permissions with form (-1 undefined, 0 denied, 1 allowed)
	**/
	function GroupPermissionList($groupid)
	{
		$permlist = $this->PermissionList();
		
		$perms = array("default" => -1);
		
		foreach($permlist as $key => $perm)
			$perms[$key]=-1;
		
		$q="SELECT ".$this->DESK->Database->Field("permission").",".$this->DESK->Database->Field("allowed")." ";
		$q.="FROM ".$this->DESK->Database->Table("permissions")." WHERE ";
		$q.=$this->DESK->Database->Field("permissiontype")."=".$this->DESK->Database->SafeQuote("group")." AND ";
		$q.=$this->DESK->Database->Field("usergroupid")."=".$this->DESK->Database->Safe($groupid);
		
		$r=$this->DESK->Database->Query($q);
		
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$perms[$row['permission']] = $row['allowed'];
		}
		
		$this->DESK->Database->Free($r);
		
		return $perms;
	}
	
	/**
	 * Get a list of security groups
	 * @return array List of groups id => name
	**/
	function GroupList()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("permgroup")." ORDER BY ".$this->DESK->Database->Field("permgroupid")." ASC";
		$r=$this->DESK->Database->Query($q);
		
		$out = array();
		
		while ($row=$this->DESK->Database->FetchAssoc($r))
			$out[$row['permgroupid']]=$row['groupname'];
		
		$this->DESK->Database->Free($r);
		
		return $out;
	}
	
	/**
	 * Delete a security group
	 * @param int $groupid Group ID
	**/
	function DeleteGroup($groupid)
	{
		// First remove users from the group
		$q="UPDATE ".$this->DESK->Database->Table("user")." SET ";
		$q.=$this->DESK->Database->Field("permgroup")."=0 WHERE ";
		$q.=$this->DESK->Database->Field("permgroup")."=".$this->DESK->Database->Safe($groupid);
		$this->DESK->Database->Query($q);
		
		// And the linked permissions
		$q="DELETE FROM ".$this->DESK->Database->Table("permissions")." WHERE ";
		$q.=$this->DESK->Database->Field("permissiontype")."=".$this->DESK->Database->SafeQuote("group")." AND ";
		$q.=$this->DESK->Database->Field("usergroupid")."=".$this->DESK->Database->SafeQuote($groupid);
		$this->DESK->Database->Query($q);
		
		// Now delete the group
		$q="DELETE FROM ".$this->DESK->Database->Table("permgroup")." WHERE ";
		$q.=$this->DESK->Database->Field("permgroupid")."=".$this->DESK->Database->Safe($groupid);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Create a group
	 * @param string $groupname Name of the group
	**/
	function CreateGroup($groupname)
	{
		$q="INSERT INTO ".$this->DESK->Database->Table("permgroup")."(".$this->DESK->Database->Field("groupname").") ";
		$q.="VALUES(".$this->DESK->Database->SafeQuote($groupname).")";
		$this->DESK->Database->Query($q);
	}
	
	
}
?>
