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

// Class to handle all emails that alloc sends
// 
// Will log emails sent, and will not attempt to send email when the server is a dev boxes
// 
class alloc_email {
  
  // If URL has any of these strings in it then the email won't be sent.
  #var $no_email_urls = array("alloc_dev");
  var $no_email_urls = array();

  // If alloc is running on any of these boxes then no emails will be sent!
  var $no_email_hosts = array("garlic.office.cyber.com.au"
                             ,"spectrum.lancewood.net"
                             ,"peach.office.cyber.com.au"
                             ,"peach"
                             #,"alloc_dev"
                             );

  // Set to true to skip host and url checking
  var $ignore_no_email_hosts = false; 
  var $ignore_no_email_urls = false; 

  // Actual email variables
  var $to_address = "";
  var $headers = "";
  var $subject = "";
  var $body = ""; 

  function alloc_email($to_address="",$subject="",$body="",$message_type="") {
    $to_address   and $this->set_to_address($to_address);
    $subject      and $this->set_subject($subject);
    $body         and $this->set_body($body);
    $message_type and $this->set_message_type($message_type);
  }
  function set_to_address($to=false) {
    $to or $to = $this->to_address;
    $to or $to = ALLOC_DEFAULT_TO_ADDRESS;
    $this->to_address = $to;
    $this->del_header("to");
  }
  function set_body($body=false) {
    $body or $body = $this->body;
    $this->body = $body;
  }
  function set_message_type($message_type=false) {
    $message_type or $message_type = $this->message_type;
    $this->message_type = $message_type;
  }
  function set_from($from=false) {
    $from or $from = $this->from;
    $from or $from = ALLOC_DEFAULT_FROM_ADDRESS;
    $this->add_header("From",$from);
    $this->from = $from;
  }
  function set_content_type($type=false) {
    $type or $type = "text/plain; charset=utf-8";
    $this->add_header("Content-Type",$type);
  }
  function set_subject($subject=false) {
    $subject or $subject = $this->subject;
    if (!preg_match("/^\[allocPSA\]/",$subject)) {
      $extra = "[allocPSA] ";
    }
    $this->subject = $extra.$subject;
    $this->del_header("subject");
  }
  function set_reply_to($email=false) {
    $email or $email = ALLOC_DEFAULT_FROM_ADDRESS;
    $this->add_header("Reply-To",$email);
  }
  function set_message_id($hash=false) {
    $hash and $hash = ".".$hash;
    list($usec, $sec) = explode(" ", microtime());
    $time = $sec.$usec;
    $time = base_convert($time,10,36);
    $rand = md5(microtime().getmypid().md5(microtime()));
    $rand = base_convert($rand,16,36);
    $bits = explode("@",ALLOC_DEFAULT_FROM_ADDRESS);
    $host = str_replace(">","",$bits[1]);
    $this->add_header("Message-ID", "<".$time.".".$rand.$hash."@".$host.">");
  }
  function send($use_default_headers=true) {

    if ($use_default_headers) {
      $this->set_to_address();
      $this->set_body();
      $this->set_message_type();
      $this->set_from();
      $this->set_content_type();
      $this->set_subject();
      $this->set_reply_to();
      $this->set_message_id();
    }

    if ($this->is_valid_url()) {
    
      // if we've added attachments to the email, end the mime boundary
      if ($this->done_top_mime_header) { 
        $this->body.= $this->get_bottom_mime_header();
      }

      #echo "<pre><br>HEADERS: ".$this->headers."</pre>";
      #echo "<pre><br>BODY: ".$this->body."</pre>";

      $result = mail($this->to_address, $this->subject, $this->body, $this->headers, "-f".ALLOC_DEFAULT_RETURN_PATH_ADDRESS);
      if ($result) {
        $this->log();
        return true;
      } 
    }
    return false;
  }
  function set_headers($headers="") {
    $headers or $headers = $this->headers;
    $headers = preg_replace("/\r\n\s+/"," ",$headers);
    $this->headers = $headers;
  }
  function get_headers() {
    return $this->headers;
  }
  function add_header($header,$value="",$replace=1) {
    if ($replace) {
      $this->del_header($header);
    }
    $this->headers = trim($this->headers)."\r\n".$header.": ".$value;
  }
  function del_header($header) {
    $this->headers = preg_replace("/\n".$header.":.*/i","",$this->headers);
  }
  function get_header($header) {
    preg_match("/\n".$header.":(.*)/i",$this->headers,$matches);
    return $matches[1];
  }
  function header_exists($header) {
    return preg_match("/\n".$header.":(.*)/i",$this->headers);
  }
  function is_valid_url() {

    // Validate against particular hosts
    in_array($_SERVER["SERVER_NAME"], $this->no_email_hosts) and $dont_send = true;
    $this->ignore_no_email_hosts and $dont_send = false;

    // Validate against particular bits in the url
    foreach ($this->no_email_urls as $url) {
      preg_match("/".$url."/",$_SERVER["SCRIPT_FILENAME"]) and $dont_send = true;
    }
    $this->ignore_no_email_urls and $dont_send = false;

    // Invert return
    return !$dont_send;
  }
  function log() {
    global $current_user;
    $sentEmailLog = new sentEmailLog();
    $sentEmailLog->set_value("sentEmailTo",$this->to_address);
    $sentEmailLog->set_value("sentEmailSubject",$this->subject);
    $sentEmailLog->set_value("sentEmailBody",$this->body);
    $sentEmailLog->set_value("sentEmailHeader",$this->headers);
    $sentEmailLog->set_value("sentEmailType",$this->message_type);
    $sentEmailLog->save();
  }
  function get_mime_boundary() {
    // This function will generate a new mime boundary
    if (!$this->mime_boundary) {
      $rand = md5(time().microtime()); 
      $this->mime_boundary = "==alloc_mime_boundary_".mktime()."_".$rand."==";
    }
    return $this->mime_boundary;
  }
  function get_top_mime_header() {
    if (!$this->done_top_mime_header) {
      $mime_boundary = $this->get_mime_boundary();
      $header = "\r\n--".$mime_boundary;
      $header.= "\nContent-Type: text/plain; charset=\"utf-8\"";
      $header.= "\nContent-Disposition: inline";
      $header.= "\n";
      $header.= "\n";
      $this->done_top_mime_header = true;
      return $header;
    }
  }
  function get_bottom_mime_header() {
    return "\n\n--".$this->get_mime_boundary()."--";
  }
  function add_attachment($file) {
    if (file_exists($file) && is_readable($file)) {
      $mime_boundary = $this->get_mime_boundary();
      $this->add_header("MIME-Version","1.0");
      $this->add_header("Content-Type","multipart/mixed; boundary=\"".$mime_boundary."\"");
  
      // Read the file to be attached ('rb' = read binary) 
      $fh = fopen($file,'rb'); 
      $data = fread($fh,filesize($file)); 
      fclose($fh);

      $mimetype = get_mimetype($file);
  
      // Base64 encode the file data 
      $data = chunk_split(base64_encode($data));
  
      $this->body = $this->get_top_mime_header().$this->body;
      $this->body.= "\n\n--".$mime_boundary;
      $this->body.= "\nContent-Type: ".$mimetype."; name=\"".basename($file)."\"";
      $this->body.= "\nContent-Disposition: attachment; filename=\"".basename($file)."\"";
      $this->body.= "\nContent-Transfer-Encoding: base64";
      $this->body.= "\n\n".$data;
    }
  }
  function get_header_mime_boundary() {
    // This function will parse the header for a mime boundary
    $content_type = $this->get_header("Content-Type");
    // If the email is a multipart, ie has attachments 
    if (preg_match("/multipart/i",$content_type) && preg_match("/boundary/i",$content_type)) {
      // Suck out the mime boundary
      preg_match("/boundary=\"?([^\"]*)\"?/i",$content_type,$matches);
      $mime_boundary = $matches[1];
      return $mime_boundary;
    }
  }
}


?>
