{if !$person_projectPersonID}{$display="display:none"}{/}
<div id="new_projectPerson{$person_projectPersonID}" style="{$display}">
<table width="100%">
<tr>
  <td><select name="person_personID[]"><option value="">{show_person_options()}</select> </td>
  <td><select name="person_roleID[]">{$person_role_options}</select></td>
  <td>{page::money($project_currencyTypeID,0,"%s")} <input type="text" size="7" name="person_rate[]" value="{page::money($project_currencyTypeID,$person_rate,"%mo")}" />{if $taxName}(ex. {$taxName}){/}</td>
  <td><select name="person_rateUnitID[]">{$rateType_options}</select></td>
  <td width="100px" align="right">
    {if $person_projectPersonID}
      <a href="#x" class="magic" onClick="$('#new_projectPerson{$person_projectPersonID}').remove();">Remove</a>
    {/}
  </td>
</tr>
</table>
</div>
