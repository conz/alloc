<tr id="productSaleItemRow{$productSaleItemID}" style="{$display}">
  <td><select name="productID[]"><option value="">{$productList_dropdown}</select></td>
  <td><input type="text" size="5" name="quantity[]" value="{$quantity}" onkeyup="update_values(this);"></td>
  <td><input type="text" size="10" name="buyCost[]" value="{$buyCost}">{$buyCostTax_check}</td>
  <td><input type="text" size="10" name="sellPrice[]" value="{$sellPrice}">{$sellPriceTax_check}</td>
  <td><input type="text" size="43" name="description[]" value="{$description}"></td>
  <td class="right nobr">
    <input type="checkbox" name=deleteProductSaleItem[] value="{$productSaleItemID}" id="deleteProductSaleItem{$productSaleItemID}">
    <label for="deleteProductSaleItem{$productSaleItemID}"> Delete</label>
    <input type="hidden" name="productSaleItemID[]" value="{$productSaleItemID}">
  </td>
</tr>
