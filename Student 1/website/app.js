(function($){

	$('.addPanier').click(function(event){
		event.preventDefault();
		$.get($(this).attr('href'),{},function(data){
			if(data.error){
				alert(data.message);
			}else{
				alert(data.message);
			}
		},'json');
		return false;
	});


})(jQuery);