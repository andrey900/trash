function PlusClickReview(it, id){
	if($(it).hasClass('rating-vote-plus-active')){	
		VoteReview(it, id, 1, "minus", "LIKE");
	}else{		
		var dislike = $(it).parent().find('.rating-vote-minus');
		
		if($(dislike).hasClass('rating-vote-minus-active'))
			VoteReview(dislike, id, 1, "minus", "DISLIKE");
		
		VoteReview(it, id, 1, "plus", "LIKE");	
	}
}

function MinusClickReview(it, id){	
	if($(it).hasClass('rating-vote-minus-active')){	
		VoteReview(it, id, 1, "minus", "DISLIKE");
	}else{	
		
		var like = $(it).parent().find('.rating-vote-plus');
		
		if($(like).hasClass('rating-vote-plus-active'))
			VoteReview(like, id, 1, "minus", "LIKE");
		
		VoteReview(it, id, 1, "plus", "DISLIKE");	
	}
}

function VoteReview(it, id, value, action, vote_prop){
	
	$(it).addClass('rating-vote-load');
	
	$.ajax({
        type: 'POST',
        url: CurComponentTemplateDir+'/misc.php',
        data: {IBLOCK_ID: arParams['IBLOCK_ID'], q: value, ID: id, method: "VoteReview", action: action, vote_prop: vote_prop},
        success: function(data) {
            	
        	var res = JSON.parse(data);
        	if(res.ok){
        		if(vote_prop == "LIKE"){
	        		if(action == "plus"){
		        		$(it).removeClass('rating-vote-load').addClass('rating-vote-plus-active');
		        		$('#rating-vote-'+id).find('.rating-vote-result-plus').html(res.like_counter);
	        		}else{
	        			$(it).removeClass('rating-vote-load').removeClass('rating-vote-plus-active');
		        		$('#rating-vote-'+id).find('.rating-vote-result-plus').html(res.like_counter);
	        		}
        		}else{
        			if(action == "plus"){
		        		$(it).removeClass('rating-vote-load').addClass('rating-vote-minus-active');
		        		$('#rating-vote-'+id).find('.rating-vote-result-minus').html(res.like_counter);
	        		}else{
	        			$(it).removeClass('rating-vote-load').removeClass('rating-vote-minus-active');
		        		$('#rating-vote-'+id).find('.rating-vote-result-minus').html(res.like_counter);
	        		}
        		}
        		//$(it).parent().parent().after("<div class='review_rating_answer_"+id+"'>"+res.ok+"</div>");
        		//setTimeout(function(){ $('.review_rating_answer_'+id).remove(); }, 3000);
        		//$("#response_reviews").html(res.ok);
        	}
        	
        },
        error:  function(xhr, str){
        	$("#response_reviews").html('Возникла ошибка: ' + xhr.responseCode);
        }
	});
}

function DeleteReview(it, id){
	
	//var ajax_id = $(it).attr('ajax_id');
	var parent = $(it).attr('parent');
	
	$.ajax({
		type: 'POST',
	    url: CurComponentTemplateDir+'/misc.php',
	    data: {IBLOCK_ID: arParams['IBLOCK_ID'], ID: id, method: "DeleteReview", ajax_id: arParams['AJAX_ID'], page: arParams['PAGE']},
	    dataType: "json",
	    success: function(data) {
	    	if(data.res){
	    		$("#response_reviews").html(data.res);
	    		
	    		
	    		BX.ajax.insertToNode(data.page+'&parent='+parent+'&bxajaxid='+data.bxajaxid, 'comp_'+data.bxajaxid); return false;
	    		//BX.ajax.insertToNode(data.page+'?PAGEN_1=1&bxajaxid='+data.bxajaxid, 'comp_'+data.bxajaxid); return false;
	    		//BX.ajax.insertToNode(data.page, 'comp_'+data.bxajaxid); return false;
	    	}else{
	    		$("#response_reviews").html(data.error);
	    	}
	    },
	    error:  function(xhr, str){
	    	$("#response_reviews").html('Возникла ошибка: ' + xhr.responseCode);
	    }
	});
}

function showReviewForm(it, callback){
	
	var status = $(it).attr('status');
	var good = $(it).attr('good');
	var review_id = $(it).attr('review_id');
	var parent = $(it).attr('parent');
	//var page = $(it).attr('page');
	
	if (typeof status !== typeof undefined && status !== false && status == 'y') {
		$(it).removeAttr('status');
		$('#reviews-reply-form').remove();
	}else{
		
		$('#response_body').find('.leave_review').removeAttr('status');
		
		$(it).attr('status', 'y');
		$('#reviews-reply-form').remove();
		
		if(parent == 1){
			var params = {IBLOCK_ID: arParams['IBLOCK_ID'], good: good, review_id: review_id, ajax_id: arParams['AJAX_ID'], page: arParams['FIRST_PAGE'], parent:parent, city: arParams['CUR_CITY']};
		}else{
			var params = {IBLOCK_ID: arParams['IBLOCK_ID'], good: good, review_id: review_id, ajax_id: arParams['AJAX_ID'], page: arParams['PAGE'], parent:parent, city: arParams['CUR_CITY']};
		}
		
		$.ajax({
		   	async: false,
		   	url: CurComponentTemplateDir+'/form.php',
		   	data: params,
		   	type: "POST",
		   	dataType: "html",
		   	success: function(data) { 
		   		if($(".first_leave_review").length)
		   			$(".first_leave_review").show();
		   		
		   		if($(it).parent().hasClass('first_leave_review')){
		   			$(it).parent().after(data);
		   			$(it).parent().hide();
		   		}else{
		   			$(it).parent().parent().after(data);
		   		}
		   		
			   	if(typeof callback == 'function'){
			   		callback.call(this);
	       		}
		   	}  
		});
	}
}

function SendReviewForm(formData){
	
	var form_data = formData.serialize();	
	var review = formData.find('.review_text').val();
	var review_author = formData.find('.review_author').val();
	var result = formData.find('.results');
	var errors = "";
  
	if(review_author.length <= 0){
		errors = errors+'<div class="error">Ошибка, имя не может быть пустым!</div>\n';
	}
	if(review.length <= 0){
		errors = errors+'<div class="error">Ошибка, отзыв не может быть пустым!</div>\n';
	}
	 
	 if(errors.length == 0){
		 
		var sign = "?";
		 
		result.empty();
		$.ajax({
			type: 'POST',
		    url: CurComponentTemplateDir+'/misc.php',
		    data: form_data,
		    dataType: "json",
		    success: function(data) {
		    	if(data.res){
		    		result.html(data.res);
		    		
		    		if(data.page.indexOf("?")>0){
		    			sign = "&";
		    		}
		    				    		
		    		if(data.parent && data.parent != 1){
		    			BX.ajax.insertToNode(data.page+sign+'bxajaxid='+data.bxajaxid+'&parent='+data.parent, 'comp_'+data.bxajaxid); return false;
		    		}else{
		    			BX.ajax.insertToNode(data.page+sign+'bxajaxid='+data.bxajaxid, 'comp_'+data.bxajaxid); return false;
		    		}
		    		
		    		//BX.ajax.insertToNode(data.page+'?PAGEN_1=1&bxajaxid='+data.bxajaxid, 'comp_'+data.bxajaxid); return false;
		    		//BX.ajax.insertToNode(data.page, 'comp_'+data.bxajaxid); return false;
		    	}else{
		    		result.html(data.error);
		    	}
		    },
		    error:  function(xhr, str){
		        result.html('Возникла ошибка: ' + xhr.responseCode);
		    }
		});
	 }else{
		result.empty();
		result.html(errors);
	}
}

function ReviewsShowHide(it, id){
	var reviews_block = $('#'+id);
	if(reviews_block.hasClass('hide')){
		reviews_block.removeClass('hide').addClass('show');
		$(it).find('span').html('Скрыть ответы');
	}else{
		reviews_block.removeClass('show').addClass('hide');
		$(it).find('span').html('Все ответы');
	}
}