<?php

/*
 * Copyright (C) 2006-2011 Alex Lance, Clancy Malcolm, Cyber IT Solutions
 * Pty. Ltd.
 * 
 * This file is part of the allocPSA application <info@cyber.com.au>.
 * 
 * allocPSA is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at
 * your option) any later version.
 * 
 * allocPSA is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public
 * License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with allocPSA. If not, see <http://www.gnu.org/licenses/>.
*/

class project_list_home_item extends home_item {

  function project_list_home_item() {
    home_item::home_item("project_list", "Project List", "project", "projectListH.tpl", "standard", 40);
  }

  function visible() {
    global $current_user;
    return (sprintf("%d",$current_user->prefs["projectListNum"]) > 0 || $current_user->prefs["projectListNum"] == "all");
  }

  function render() {
    global $current_user, $TPL;
    if (isset($current_user->prefs["projectListNum"]) && $current_user->prefs["projectListNum"] != "all") {
      $options["limit"] = sprintf("%d",$current_user->prefs["projectListNum"]);
    }
    $options["projectStatus"] = "Current";
    $options["personID"] = $current_user->get_id();
    $TPL["projectListRows"] = project::get_list($options);
    if ($TPL["projectListRows"]) {
      return true;
    }
  }
}



?>
