<div class="d-flex flex-row mt-1 mb-3">
   <div class=""><span class="fa fa-users fa-lg fa-fw text-success"></span></div>
   <div class="ml-2"><{$block.online_total}></div>
</div>
<p>
   <{if $block.online_guests > 1}>
      <span class="fa fa-users fa-fw text-secondary"></span> <{$block.online_guests}> <{$smarty.const.THEME_OL_GUESTS}>
   <{else}>
      <span class="fa fa-user fa-fw text-secondary"></span> <{$block.online_guests}>  <{$smarty.const.THEME_OL_GUEST}>
   <{/if}>
   <br />
   <{if $block.online_members > 1}>
      <span class="fa fa-users fa-fw text-info"></span> <{$block.online_members}> <{$smarty.const.THEME_OL_MEMBERS}>
   <{else}>
      <span class="fa fa-user fa-fw text-info"></span> <{$block.online_members}> <{$smarty.const.THEME_OL_MEMBER}>
   <{/if}>
</p>
<p>
   <{$block.online_names}>
   <button type="button" class="btn btn-primary btn-sm" onclick="getOnlineData();" title="<{$block.lang_more}>">
      <span class="fa fa-search-plus fa-lg fa-fw "></span></button>
</p>
<script>
   // load mustache to make life easier with simple js templating
   // https://github.com/aishikaty/tiny-mustache
   $scriptElement = 'include_mustache.min.js';
   var el = document.getElementById($scriptElement);
   if (el === null) {
      var xscript = document.createElement('script');
      xscript.id = $scriptElement;
      xscript.type = 'text/javascript';
      xscript.src = '<{$xoops_url}>/include/mustache.min.js';
      document.body.appendChild(xscript);
   }

   var onlineStart = 0;
   var onlineLimit = 20;

   function getOnlineData() {
      var postVals = {
         Authorization: "<{jwt xmf_key=online aud=miscajax.php uid=fill}>",
         type: "online",
         start: onlineStart,
         limit: onlineLimit
      }

      var onlineData = $.ajax({
         type: 'POST',
         url: "<{$xoops_url}>/miscajax.php?type=online",
         data: postVals,
         dataType: "text",
         success: function (resultData) {
            alert("Make stuff happen!");
            const inputjson = JSON.parse(resultData);
            const array1 = inputjson.onlineUserInfo;

            array1.forEach(formatOutput);

            console.log(resultData);
         }
      });

      function formatOutput(item, index) {
         console.log(item);
         var template = '<tag>{{uid}} {{uname}} {{dirname}} {{avatar}}</tag>';
         var rendered = mustache(template, item);
         console.log(rendered);
      }
   }
</script>
