if (typeof jQuery != "undefined"){
jQuery(function(){
 window.setTimeout(
  function(){
   jQuery(".proximic_evenrowhigh td").animate({backgroundColor:"#ffffff"}, "slow");
   jQuery(".proximic_oddrowhigh td").animate({backgroundColor:"#eeffee"}, "slow");
   jQuery(".proximic_disabledhigh td").animate({backgroundColor:"#f5f5f5"}, "slow");
 }, 5000);
});
}

function proximic_changename(that){
 var nn = that.value||"name";
 nn = nn.replace(/ /g, "_");
 if (typeof jQuery != "undefined"){
  jQuery("span.proximic_name").html(nn);
 }
}