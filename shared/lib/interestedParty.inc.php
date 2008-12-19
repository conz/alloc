<?php

/*
 * Copyright (C) 2006, 2007, 2008 Alex Lance, Clancy Malcolm, Cybersource
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

class interestedParty extends db_entity {
  public $data_table = "interestedParty";
  public $key_field = "interestedPartyID";
  public $data_fields = array("entityID"
                             ,"entity"
                             ,"fullName"
                             ,"emailAddress"
                             ,"personID"
                             ,"clientContactID"
                             ,"external"
                             );

  function exists($entity, $entityID, $email) {
    $db = new db_alloc();
    $db->query("SELECT *
                  FROM interestedParty
                 WHERE entityID = %d
                   AND entity = '%s'
                   AND emailAddress = '%s'
               ",$entityID,db_esc($entity),db_esc($email));
    return $db->row();
  }

  function make_interested_parties($entity,$entityID,$encoded_parties=array()) {
    // Nuke entries from interestedParty
    $q = sprintf("DELETE FROM interestedParty WHERE entity = '%s' AND entityID = %d",db_esc($entity),$entityID);
    $db = new db_alloc();
    $db->query($q);

    // Add entries to interestedParty
    if (is_array($encoded_parties)) {
      foreach ($encoded_parties as $encoded) {
        $info = interestedParty::get_decoded_interested_party_identifier($encoded);
        $interestedParty = new interestedParty;
        $interestedParty->set_value("entity",$entity);
        $interestedParty->set_value("entityID",$entityID);
        $interestedParty->set_value("fullName",$info["name"]);
        $interestedParty->set_value("emailAddress",$info["email"]);
        $interestedParty->set_value("personID",$info["personID"]);
        $interestedParty->set_value("clientContactID",$info["clientContactID"]);
        $info["external"] and $interestedParty->set_value("external","1");
        $interestedParty->save();
      }
    }
  }

  function sort_interested_parties($a, $b) {
    return strtolower($a["name"]) > strtolower($b["name"]);
  }

  function get_interested_parties($entity,$entityID=false,$ops=array()) {
    $rtn = array();

    if ($entityID) {
      $db = new db_alloc();
      $q = sprintf("SELECT *
                      FROM interestedParty
                     WHERE entity='%s'
                       AND entityID = %d
                  ",db_esc($entity),$entityID);
      $db->query($q);
      while ($db->row()) {
        $ops[$db->f("emailAddress")]["name"] = $db->f("fullName");
        $ops[$db->f("emailAddress")]["role"] = "interested";
        $ops[$db->f("emailAddress")]["selected"] = true;
        $ops[$db->f("emailAddress")]["personID"] = $db->f("personID");
        $ops[$db->f("emailAddress")]["clientContactID"] = $db->f("clientContactID");
        $ops[$db->f("emailAddress")]["external"] = $db->f("external");
      }
    }

    if (is_array($ops)) {
      foreach ($ops as $email => $info) {
        // if there is an @ symbol in email address
        if (stristr($email,"@")) { 
          $info["email"] = $email;
          $info["identifier"] = interestedParty::get_encoded_interested_party_identifier($info);
          $rtn[$email] = $info;
        }
      }

      uasort($rtn,array("interestedParty","sort_interested_parties"));
    }
    return $rtn;
  }

  function get_encoded_interested_party_identifier($info=array()) {
    return urlencode(base64_encode(serialize($info)));
  }

  function get_decoded_interested_party_identifier($blob) {
    return unserialize(base64_decode(urldecode($blob)));
  }

  function get_interested_parties_html($parties=array()) {
    foreach ($parties as $email => $info) {
      if ($info["name"]) {
        unset($sel,$c);
        $counter++;
        $info["selected"] and $sel = " checked";
        $info["external"] and $c.= " warn";
        $str.= "<div width=\"150px\" class=\"nobr ".$c."\" id=\"td_ect_".$counter."\" style=\"float:left; width:150px; margin-bottom:5px;\">";
        $str.= "<input id=\"ect_".$counter."\" type=\"checkbox\" name=\"commentEmailRecipients[]\" value=\"".$info["identifier"]."\"".$sel."> ";
        $str.= "<label for=\"ect_".$counter."\" title=\"" . $info["name"] . " &lt;" . $info["email"] . "&gt;\">".$info["name"]."</label></div>";
      }
    }
    return $str;
  }

  function delete_interested_party($entity, $entityID, $emailAddress) {
    $q = sprintf("DELETE 
                    FROM interestedParty 
                   WHERE entity='%s' 
                     AND entityID='%d' 
                     AND emailAddress='%s'",db_esc($entity),$entityID,db_esc($emailAddress));
    $db = new db_alloc();
    $db->query($q);
  }

  function adjust_by_email_subject($subject="",$entity,$entityID,$fullName="",$emailAddress="",$personID="",$clientContactID="") {

    if (preg_match("/(unsub|unsubscribe)\s*$/i",$subject)) {
      if (interestedParty::exists($entity, $entityID, $emailAddress)) {
        interestedParty::delete_interested_party($entity, $entityID, $emailAddress);
        $action = "unsubscribed";
      }

    } else if (preg_match("/(sub|subscribe)\s*$/i",$subject)) {
      if (!interestedParty::exists($entity, $entityID, $emailAddress)) {
        $interestedParty = new interestedParty;
        $interestedParty->set_value("entity",$entity);
        $interestedParty->set_value("entityID",$entityID);
        $interestedParty->set_value("fullName",$fullName);
        $interestedParty->set_value("emailAddress",$emailAddress);
        $interestedParty->set_value("personID",$personID);
        $interestedParty->set_value("clientContactID",$clientContactID);
        $interestedParty->save();
        $action = "subscribed";
      }
    }
    return $action;
  }


}



?>
