function PlusClickPreviewReview(it, id){
	if($(it).hasClass('rating-vote-plus-active')){	
		VotePreviewReview(it, id, 1, "minus", "LIKE");
	}else{		
		var dislike = $(it).parent().find('.rating-vote-minus');
		
		if($(dislike).hasClass('rating-vote-minus-active'))
			VotePreviewReview(dislike, id, 1, "minus", "DISLIKE");
		
		VotePreviewReview(it, id, 1, "plus", "LIKE");	
	}
}

function MinusClickPreviewReview(it, id){	
	if($(it).hasClass('rating-vote-minus-active')){	
		VotePreviewReview(it, id, 1, "minus", "DISLIKE");
	}else{	
		
		var like = $(it).parent().find('.rating-vote-plus');
		
		if($(like).hasClass('rating-vote-plus-active'))
			VotePreviewReview(like, id, 1, "minus", "LIKE");
		
		VotePreviewReview(it, id, 1, "plus", "DISLIKE");	
	}
}

function VotePreviewReview(it, id, value, action, vote_prop){
	
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
		        		$('#preview_rating-vote-'+id).find('.rating-vote-result-plus').html(res.like_counter);
	        		}else{
	        			$(it).removeClass('rating-vote-load').removeClass('rating-vote-plus-active');
		        		$('#preview_rating-vote-'+id).find('.rating-vote-result-plus').html(res.like_counter);
	        		}
        		}else{
        			if(action == "plus"){
		        		$(it).removeClass('rating-vote-load').addClass('rating-vote-minus-active');
		        		$('#preview_rating-vote-'+id).find('.rating-vote-result-minus').html(res.like_counter);
	        		}else{
	        			$(it).removeClass('rating-vote-load').removeClass('rating-vote-minus-active');
		        		$('#preview_rating-vote-'+id).find('.rating-vote-result-minus').html(res.like_counter);
	        		}
        		}
        		//$(it).parent().parent().after("<div class='review_rating_answer_"+id+"'>"+res.ok+"</div>");
        		//setTimeout(function(){ $('.review_rating_answer_'+id).remove(); }, 3000);
        		//$("#response_reviews").html(res.ok);
        	}
        	
        },
        error:  function(xhr, str){
        	//$("#response_reviews").html('Возникла ошибка: ' + xhr.responseCode);
        }
	});
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