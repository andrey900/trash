var AniartRating = {
  
  classes:{'vote':'.count_show.like_show b', 'answer':'.count_show .answer', "disable":"disabled",
       'val':'#rating input[name="val"]', 'votes':'#rating input[name="votes"]',
       'voteid':'#rating input[name="vote-id"]', 'votetype':'#rating input[name="vote-type"]',
       'voteLock': ".count_show.like_show"},
  vote:0,
  lock:false,
  ratingdata:{},

  send :function(data){
    //console.log(this.lock);
    if( this.lock == true )
      return false;

    if( !data )
      data = this.getData();

    $.ajax({
      type: "POST",
      url: "/ajax/rate.php",
      data: data
    }).done(function( msg ) {
        var answer = JSON.parse(msg);
        if(answer.status=='OK'){
          $(AniartRating.classes.vote).text(AniartRating.vote);
        } 
        
        AniartRating.lock = true;
        $(AniartRating.classes.voteLock).addClass(AniartRating.classes.disable);
        //AniartRating.answer(answer.msg);
    });
  },
  setVote:function(){
    this.vote = parseInt($(this.classes.vote).text())+1;
    return this.vote;
  },
  answer:function(data){
    $(this.classes.vote).after('<div class="answer"></div>');
    $(this.classes.answer).text(data);
    //setInterval(function(){$(AniartRating.classes.answer).remove()}, 10000);
  },
  getData:function(){
    this.ratingdata = { "score": "5",
                "val" : $(this.classes.val).val(),
                "vote-id":  $(this.classes.voteid).val(),
                "vote-type": $(this.classes.votetype).val(),
                "votes": this.setVote() }
    
    return this.ratingdata;
  }
} 
