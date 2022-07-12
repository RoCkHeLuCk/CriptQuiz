//$(".mask-cel").mask("(00)00000-0000");
//$(".mask-tel").mask("(00)0000-0000");

function MaskPhone(obj)
{
   var phone = $(obj);
   phone.unmask();
   var num = phone.val().replace(/\D/g, "");
   if (num.length > 9)
   {
      phone.mask('(00) 0-0000-0000');
   }else{
      phone.mask('(00) 0000-00000');
   }
   var end = phone.val().length;
   obj.setSelectionRange(end,end);
}
