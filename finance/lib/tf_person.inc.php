<?php

/*
 *
 * Copyright 2006, Alex Lance, Clancy Malcolm, Cybersource Pty. Ltd.
 * 
 * This file is part of AllocPSA <info@cyber.com.au>.
 * 
 * AllocPSA is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 * 
 * AllocPSA is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * AllocPSA; if not, write to the Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 */

class tf_person extends data_entity {
  var $data_table = "tfPerson";
  var $display_field_name = "personID";


  function tf_person() {
    $this->key_field = new db_text_field("tfPersonID");
    $this->data_fields = array("tfID"=>new db_text_field("tfID")
                               , "personID"=>new db_text_field("personID"));

  }
}



?>
