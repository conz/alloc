
<form enctype="multipart/form-data" action="{url_alloc_project}" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
<input type="hidden" name="projectID" value="{projectID}">

{table_box}
  <tr>
    <th colspan="2">Project Files</th>
  </tr>
  <tr>
    <td width="3%">Size</td>
    <td>File</td>
  </tr>
{:list_attachments templates/project_attachmentsR.tpl}
  <tr>
    <td colspan="2" align="right" valign="middle">
		  <table align="right" cellpadding="0" cellspacing="0">
		    <tr>
		      <td><input type="file" name="attachment"></td>
		      <td><input type="submit" value="Save Document" name="save_attachment"></td>
		    </tr>
		  </table>
    </td>
  </tr>
</table>
</form>
