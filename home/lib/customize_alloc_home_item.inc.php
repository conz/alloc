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

class customize_alloc_home_item extends home_item {

  function __construct() {
    parent::__construct("", "Preferences", "home", "customizeH.tpl", "narrow",60, false);
  }

  function visible() {
    $current_user = &singleton("current_user");
    return is_object($current_user);
  }

  function render() {
    global $TPL;
    $current_user = &singleton("current_user");

    $customizedFont_array = page::get_customizedFont_array();
    $TPL["fontOptions"] = page::select_options($customizedFont_array, $current_user->prefs["customizedFont"]);
    $TPL["fontLabel"] = $customizedFont_array[$current_user->prefs["customizedFont"]];

    $customizedTheme_array = page::get_customizedTheme_array();
    if (!isset($current_user->prefs["customizedTheme2"])) {
      $current_user->prefs["customizedTheme2"] = 4;
    }
    $TPL["themeOptions"] = page::select_options($customizedTheme_array, $current_user->prefs["customizedTheme2"]);
    $TPL["themeLabel"] = $customizedTheme_array[$current_user->prefs["customizedTheme2"]];

    $week_ops = array("0"=>0, 1=>1, 2=>2, 3=>3, 4=>4, 8=>8, 12=>12, 30=>30, 52=>52);
    $TPL["weeksOptions"] = page::select_options($week_ops, $current_user->prefs["tasksGraphPlotHome"]);
    $TPL["weeksLabel"] = $week_ops[$current_user->prefs["tasksGraphPlotHome"]];

    $TPL["weeksBackOptions"] = page::select_options($week_ops, $current_user->prefs["tasksGraphPlotHomeStart"]);
    $TPL["weeksBackLabel"] = $week_ops[$current_user->prefs["tasksGraphPlotHomeStart"]];

    $task_num_ops = array("0"=>0,1=>1,2=>2,3=>3,4=>4,5=>5,10=>10,15=>15,20=>20,30=>30,40=>40,50=>50,"all"=>"All");
    $TPL["topTasksNumOptions"] = page::select_options($task_num_ops, $current_user->prefs["topTasksNum"]);
    $TPL["topTasksNumLabel"] = $task_num_ops[$current_user->prefs["topTasksNum"]];

    $task_status_array = task::get_task_statii_array();
    $TPL["topTasksStatusOptions"] = page::select_options($task_status_array, $current_user->prefs["topTasksStatus"]);
    if(count($current_user->prefs["topTasksStatus"]) > 1) {
      foreach ((array)$current_user->prefs["topTasksStatus"] as $v) {
        $TPL["topTasksStatusLabel"].= $sep.str_replace("&nbsp;"," ",$task_status_array[$v]);
        $sep = ", ";
      }
    } else {
      $TPL["topTasksStatusLabel"] = $task_status_array[$current_user->prefs["topTasksStatus"][0]];
    }

    $project_list_ops = array("0"=>0,5=>5,10=>10,15=>15,20=>20,30=>30,40=>40,50=>50,"all"=>"All");
    $TPL["projectListNumOptions"] = page::select_options($project_list_ops, $current_user->prefs["projectListNum"]);
    $TPL["projectListNumLabel"] = $project_list_ops[$current_user->prefs["projectListNum"]];
    
    $dailyTEO = array("yes"=>"Yes", "no"=>"No");
    $TPL["dailyTaskEmailOptions"] = page::select_options($dailyTEO, $current_user->prefs["dailyTaskEmail"]);
    $TPL["dailyTaskEmailLabel"] = $dailyTEO[$current_user->prefs["dailyTaskEmail"]];

    $TPL["receiveOwnTaskCommentsOptions"] = page::select_options($dailyTEO, $current_user->prefs["receiveOwnTaskComments"]);
    $TPL["receiveOwnTaskCommentsLabel"] = $dailyTEO[$current_user->prefs["receiveOwnTaskComments"]];

    $TPL["showFiltersOptions"] = page::select_options($dailyTEO, $current_user->prefs["showFilters"]);
    $TPL["showFiltersLabel"] = $dailyTEO[$current_user->prefs["showFilters"]];

    $TPL["privateMode"] = $current_user->prefs["privateMode"];

    $TPL["timeSheetHoursWarn"] = $current_user->prefs["timeSheetHoursWarn"];
    $TPL["timeSheetDaysWarn"] = $current_user->prefs["timeSheetDaysWarn"];
    return true;
  }
}



?>
