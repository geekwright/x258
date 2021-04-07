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

<!-- Modal -->
<div class="modal fade" id="onlineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="onlineModalTitle">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div id="onlinecontent" class="modal-body">
         </div>
         <div class="modal-footer">
            <button id="onlineClose" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">(for paging)</button>
         </div>
      </div>
   </div>
</div>

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
      var template = '{{#onlineUserInfo}} \
         <div class="text-center"> \
            <div class="card col-md-8 mx-auto"> \
                 {{#uid}} \
                 <div class="card-body"> \
                 <img class="img-thumbnail" src="{{upload_url}}{{avatar}}" alt="{{lang_avatar}}" width="128"> \
                 <p class="card-text"> \
                    <a href="{{xoops_url}}/user.php?uid={{uid}}">{{uname}}</a> \
                 {{/uid}} \
                 {{#anon}} \
                 <div class="card-body"> \
                 <p class="card-text"> \
                 {{uname}} \
                 {{/anon}}<br>{{dirname}} \
                 {{#isadmin}}<br>{{ip}}<br>{{updated}}{{/isadmin}} \
                 </div> \
            </div> \
         </div> \
         {{/onlineUserInfo}}';

   }
   var onlineStart = 0;

   var onlineLimit = 20;
   function getOnlineData() {

      var postVals = {
         Authorization: "<{jwt xmf_key=miscajax aud=miscajax.php uid=fill}>",
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
            const inputjson = JSON.parse(resultData);
            formatOutput(inputjson);
            console.log(resultData);
         }
      });

      function formatOutput(inputjson) {
         var rendered = mustache(template, inputjson);
         $('#onlinecontent').html(rendered);
         $('#onlineModalTitle').html(inputjson.lang_whoisonline);
         $('#onlineClose').html(inputjson.lang_close);
         $('#onlineModal').modal('show');
         console.log(rendered);
      }
   }
</script>
