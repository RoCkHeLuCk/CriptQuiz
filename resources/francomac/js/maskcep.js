$(".mask-cep").mask("00000-000");
$(".mask-number").mask("0000000");

function limpa_formulario_cep()
{
   // Limpa valores do formulário de cep.
   $("#Rua").val("");
   $("#Bairro").val("");
   $("#Cidade").val("");
   $("#Estado").val("");
   $("#Pais").val("");
   $("#Salvar").attr("disabled", false);
}

function CEP(obj)
{
   //Nova variável "cep" somente com dígitos.
   var cep = $(obj).val().replace(/\D/g, '');

   //Verifica se campo cep possui valor informado.
   if (cep != "")
   {
      $("#Salvar").attr("disabled", true);
      //Expressão regular para validar o CEP.
      var validacep = /^[0-9]{8}$/;

      //Valida o formato do CEP.
      if(validacep.test(cep))
      {
         //Preenche os campos com "..." enquanto consulta webservice.
         $("#Rua").val("...");
         $("#Bairro").val("...");
         $("#Cidade").val("...");
         $("#Estado").val("...");
         $("#Pais").val("...");


         //Consulta o webservice viacep.com.br/
         $.getJSON("https://viacep.com.br/ws/"+
                    cep +"/json/?callback=?",
            function(dados)
            {
               if (!("erro" in dados))
               {
                  //Atualiza os campos com os valores da consulta.
                  $("#Rua").val(dados.logradouro);
                  $("#Bairro").val(dados.bairro);
                  $("#Cidade").val(dados.localidade);
                  $("#Estado").val(dados.uf);
                  $("#Pais").val("Brasil");
                  $("#Salvar").attr("disabled", false);
               }else{
                  //CEP pesquisado não foi encontrado.
                  limpa_formulario_cep();
                  alert("CEP nao encontrado.");
               }
            }
         );

      }else{
         //cep é inválido.
         limpa_formulario_cep();
         alert("Formato de CEP invalido.");
      }
   }else{
      //cep sem valor, limpa formulário.
      limpa_formulario_cep();
   }
}
