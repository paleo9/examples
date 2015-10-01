//$(document).ready(function(){


  $("#namesearch").keyup(function(e){
    //Display the AJAX loader
    $('#results').oLoader({
      backgroundColor:'#000',
			  image: '/resources/orchid/images/ownageLoader/loader1.gif',
			  fadeInTime: 250,
			  fadeOutTime: 250,
			  fadeLevel: 0.7
   });


      var txt=$("#namesearch").val();
      e.preventDefault();
      $.post("/orchid/jax/employees/search", {namesearch: txt})
      .done(function(data){
        $("#results").html(data);		//Display the search results
      	$('#results').oLoader('hide');		//Hide the AJAX loader
      });


}); // keyup


  $('#fullSearch').submit(function(e){

    e.preventDefault();
    $.post("/orchid/jax/employees/advSearch", $('#fullSearch').serialize())
    .done(function(data){
      $("#results").html(data);
    }); // done
  }); // submit


  $('#results').click(function() {
      var href = $(this).find("a").attr("href");
      if(href) {
          window.location = href;
      }
  });

//}); // document ready
