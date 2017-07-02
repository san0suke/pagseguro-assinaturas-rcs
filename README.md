# pagseguro-assinaturas-rcs
### Pagseguro Assinaturas ###

<a target="_blank" href="https://pag.ae/bglK2vV">
<img alt="Doar com Paypal" src="https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif"/></a>

Este é um plugin para Wordpress que realiza pagamentos recorrentes (assinaturas).

### WooCommerce PagSeguro Assinaturas ###

**Contributors:** Robson Cesar de Siqueira 

**Donate link:** [https://pag.ae/bglK2vV](https://pag.ae/bglK2vV) 

**Tags:** woocommerce, pagseguro, assinaturas, payment 

**License:** GPLv2 or later 

**License URI:** http://www.gnu.org/licenses/gpl-2.0.html 

Adds PagSeguro gateway to the WooCommerce plugin

### Description ###

Add PagSeguro Signatures gateway to WooCommerce

This plugin adds PagSeguro Signatures gateway to WooCommerce.

Please notice that WooCommerce must be installed and active.

### Contribute ###

You can contribute to the source code in our GitHub page.

### Descrição em Português: ###

Adicione o PagSeguro Assinaturas como método de pagamento em sua loja WooCommerce.

PagSeguro Assinaturas é um método de pagamento brasileiro desenvolvido pela UOL.

O plugin WooCommerce PagSeguro Assinaturas foi desenvolvido sem nenhum incentivo do PagSeguro ou da UOL. Nenhum dos desenvolvedores deste plugin possuem vínculos com estas duas empresas.

Este plugin foi desenvolvido a partir da documentação oficial do PagSeguro Assinaturas e utiliza a última versão da API de pagamentos.

Estão disponíveis as seguintes modalidades de pagamento:

Padrão: Cliente é redirecionado ao PagSeguro para concluir a compra.

Além que é possível utilizar o [sandbox do PagSeguro](https://sandbox.pagseguro.uol.com.br/vendedor/configuracoes.html).


### Colaborar ###

Este plugin ainda precisa de muitas melhorias e não foi intensamente testado.

Por favor, se encontrarem bugs entrem em contato.

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/san0suke/pagseguro-assinaturas-rcs).


### Requerimentos: ###

É necessário possuir uma conta no [PagSeguro](http://pagseguro.uol.com.br/) e ter instalado o [WooCommerce](http://wordpress.org/plugins/woocommerce/).


### Configurações no PagSeguro: ###

Isto é importante! Sem as etapa abaixo o status dos seus pedidos não serão atualizados.

Entre em "WooCommerce" > "Configurações" > "Checkout" > "Pagseguro Assinaturas".
O plugin irá gerar a url de notificação correta para seu site no formato: "https://seusite.com.br/?wc-api=PGA_Gateway"
![Configurações do plugin.](https://uploaddeimagens.com.br/images/000/972/674/original/Screenshot_1.png?1498939879)

Se você desejar testar o plugin antes de usar oficialmente faça a etapa seguinte.

Preencha o campo "Definir URL para receber as notificações" com a URL que o plugin gerou.
Também copie seu token e seu email para informar na tela do plugin:
![Informar a URL de noficações no sandbox](http://uploaddeimagens.com.br/images/000/972/688/original/Screenshot_3.png?1498940426)

Quando você decidir terminar os testes, e colocar no ambiente real (produção), continue para esta etapa.

Entre na sua [conta real do Pagseguro](https://pagseguro.uol.com.br/preferencias/integracoes.jhtml), em seguida "Preferências" > "Integrações" e preencha o campo "Notificação de transação". 

Também obtenha seu token para informá-lo no plugin.
![Informar a URL de noficações no sandbox](http://uploaddeimagens.com.br/images/000/973/149/original/Screenshot_4.png?1499005146)


### TODO LIST ###

Esta é a lista de coisas que eu imagino que sejam possíveis de desenvolver e que eu espero obter ajuda da comunidade de desenvolvedores, e também espero desenvolver algumas delas.

1. Adicionar o Chekout por Lightbox para que os clientes não precisem sair do seu site.

2. Adicionar o checkout Transparente para que não precisem nem ver o site do pagseguro.

3. Verificar e adicionar as seguintes opções se existirem: Taxa de contratação, Período grátis, Periodicidade das cobranças

4. Colocar na loja do Wordpress quando a comunidade confirmar que está tudo funcionado.

### LOJA DE EXEMPLO ###

Se deseja ver o plugin em funcionamento no Wordpress você pode entrar em:

[https://erpnanuv.com.br/ecommerce/](https://erpnanuv.com.br/ecommerce/)

**Comprador de teste**
Para testar o checkout com um comprador cadastrado, utilize o e-mail abaixo. Caso sinta a necessidade de fazer um checkout com um usuário não cadastrado, basta informar qualquer e-mail no formato xxxxxxx@sandbox.pagseguro.com.br.

Este comprador de teste só pode ser utilizado para se autenticar no checkout do PagSeguro Sandbox.

Email: c33483869638694030024@sandbox.pagseguro.com.br

Senha: 4988B671461u9537

Código de segurança do cartão: Qualquer com 3 dígitos

CPF: qualquer válido

Telefone: qualquer válido

### CONDIÇÕES DE USO ###

Não me reposabilizo por bugs que possam existir no plugin (se encontrar algum, informe para que possa ser corrigido).

**Se concorda com este termo pode usar.**
