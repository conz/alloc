{include_template("templates/productSaleItemR2.tpl")}

<form action="{$url_alloc_productSale}" method="post">
<input type="hidden" name="productSaleID" value="{$productSaleID}">
<input type="hidden" name="productSaleItemID" value="{$productSaleItemID}">

<table class="list" style="margin:3px 0px 10px 0px;">
  <tr>
    <th>Amount</th>
    <th>Source TF</th>
    <th>Destination TF</th>
    <th>Description</th>
    <th>Status</th>
    <th class="right">
      <a href="#x" class="magic" onClick="$('#transactions_footer_{$productSaleItemID}').before('<tr>'+$('#transactionRow').html()+'</tr>');">New</a>
    </th>
  </tr>
  {show_transaction_list($transactions, "templates/transactionR.tpl")}
  {show_transaction_new("templates/transactionR.tpl")}
  <tr id="transactions_footer_{$productSaleItemID}">
    <th colspan="7" class="center">
      <input type="submit" name="save_transactions" value="Save Transactions">
      <input type="submit" name="create_default_transactions" value="Create Default Transactions">
      <input type="submit" name="delete_transactions" value="Delete All Transactions" class="delete_button">
    </th>
  </tr>
</table>
</form>
