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

// Class to handle caching
// 
class alloc_cache {

  var $db = "db_alloc";
  var $tables_to_cache = array();
  var $cache; 

  
  // Initializer
  function __construct() {
  }

  // Singleton
  function get_cache() {
    static $instance;
    if (!$instance) {
      $instance = new alloc_cache();
    }
    return $instance;
  }

  // Loads up assoc array[$table][primarykey] = row;
  function load_cache($table,$anew=false) {
    $db = new $this->db;

    if (!$this->cache[$table] || $anew) {
      if (meta::$tables[$table]) {
        $m = new meta($table);
        $this->cache[$table] = $m->get_list();

      } else {
        $db->query("SELECT * FROM ".$table);
        while ($row = $db->next_record()) {
          $this->cache[$table][$db->f($table."ID")] = $row;
        }
      }
    }
  }

  function get_cached_table($table) {
    return $this->cache[$table];
  }

  function set_cached_table($name, $table) {
    $this->cache[$name] = $table;
  }


}


?>
