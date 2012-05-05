{page::header()}
{page::toolbar()}

<script type="text/javascript" language="javascript">
$(document).ready(function() {
  {if !$reminderID}
    toggle_view_edit();
    $('#reminder_subject').focus();
  {else}
    $('#editReminder').focus();
  {/}
});
</script>


<style>
.pane {
  min-width:400px;
  width:47%;
  float:left;
  margin:0px 12px;
  vertical-align:top;
}
</style>


<form action="{$url_alloc_reminder}" method="post">



<table class="box view">
  <tr>
    <th class="header">{$reminder_title}</th>
  </tr>
  <tr>
    <td valign="top">
      <div class="pane">
        <div class="enclose">
          <h6>Name<div style="width:20%;">Enabled</div></h6>
          <div style="float:left; width:77%;">
            {=$reminder_default_subject}
          </div>
          <div style="float:right; width:20%; text-align:left;">
            {print $reminderActive ? "yes" : "no"}
          </div>
        </div>

        <h6>Description</h6>
        <pre class="comment">{=$reminder_default_content}</pre>
      </div>

      <div class="pane">
        <div class="enclose">
          <h6>Recipient<div>When</div></h6>
          <div style="float:left; width:47%;">
            {echo page::htmlentities($reminder_recipients[$reminder_recipient])}
          </div>
          <div style="float:right; width:50%; text-align:left;">
            {$reminderTime}
          </div>
        </div>

        <h6>Advanced Notice<div>Recurring Every</div></h6>
        <div style="float:left; width:47%;">
          {if $reminder_advnotice_value}
          {$reminder_advnotice_value} {$reminderAdvNoticeInterval}
          {/}
        </div>
        <div style="float:right; width:50%; text-align:left;">
          {if $reminder_recuring_value}
          {$reminder_recuring_value} {$reminderRecuringInterval}
          {/}
        </div>

      </div>
    </td>
  </tr>
  <tr>
    <td align="center" class="padded">
      <div style="margin:20px">
        <input type="button" id="editReminder" value="Edit Reminder" onClick="toggle_view_edit();">
        {$reminder_goto_parent}
      </div>
    </td>
  </tr>
</table>




<table class="box edit">
  <tr>
    <th class="header">{$reminder_title}</th>
  </tr>
  <tr>
    <td valign="top">
      <div class="pane">
        <div class="enclose">
          <h6>Name<div style="width:20%;">Enabled</div></h6>
          <div style="float:left; width:77%;">
            <input id="reminder_subject" name="reminder_subject" type="text" maxlength="255" value="{$reminder_default_subject}" style="width:100%;">
          </div>
          <div style="float:right; width:20%; text-align:left;">
            <input type="checkbox" value="1" name="reminderActive" {$reminderActive and print "checked"}>
          </div>
        </div>

        <h6>Description</h6>
        {page::textarea("reminder_content",$reminder_default_content,array("height"=>"small"))}
      </div>

      <div class="pane">

        <div class="enclose">
          <h6>Recipient<div>When</div></h6>
          <div style="float:left; width:47%;">
            <select name="reminder_recipient">{page::select_options($reminder_recipients,$reminder_recipient)}</select>
            {page::help("reminder_recipient")}
          </div>
          <div style="float:right; width:50%; text-align:left;">
            {page::calendar("reminder_date",$reminder_date)}&nbsp;&nbsp;
            <select name="reminder_hour">{$reminder_hours}</select>
            <select name="reminder_minute">{$reminder_minutes}</select>
            <select name="reminder_meridian">{$reminder_meridians}</select>
          </div>
        </div>

        <h6>Advanced Notice<div>Recurring Every</div></h6>
        <div style="float:left; width:47%;">
          <input type="text" size="4" name="reminder_advnotice_value" value="{$reminder_advnotice_value}">
          <select name="reminder_advnotice_interval">{$reminder_advnotice_intervals}</select>
        </div>
        <div style="float:right; width:50%; text-align:left;">
          <input type="text" size="4" name="reminder_recuring_value" value="{$reminder_recuring_value}">
          <select name="reminder_recuring_interval">{$reminder_recuring_intervals}</select>
        </div>

      </div>
    </td>
  </tr>
  <tr>
    <td align="center" class="padded">
      <div style="margin:20px">
        {$reminder_buttons}&nbsp;&nbsp;&nbsp;{$reminder_goto_parent}
        <input type="hidden" name="parentType" value="{$parentType}">
        <input type="hidden" name="parentID" value="{$parentID}">
        <input type="hidden" name="returnToParent" value="{$returnToParent}">
        <input type="hidden" name="step" value="4">
        <input type="hidden" name="reminderTime" value="{$reminderTime}">
        <input type="hidden" name="personID" value="{$personID}">
      </div>
    </td>
  </tr>
</table>
<input type="hidden" name="sessID" value="{$sessID}">
</form>

{page::footer()}