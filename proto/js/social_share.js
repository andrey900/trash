/* пример вызова 
<a onclick="Share.vkontakte('URL','TITLE','IMG_PATH','DESC')"> {шарь меня полностью}</a>
<a onclick="Share.facebook('URL','TITLE','IMG_PATH','DESC')"> {шарь меня полностью}</a>
<a onclick="Share.mailru('URL','TITLE','IMG_PATH','DESC')"> {шарь меня полностью}</a>
<a onclick="Share.odnoklassniki('URL','DESC')"> {шарь меня полностью}</a>
<a onclick="Share.twitter('URL','TITLE')"> {шарь меня полностью}</a> 
*/
Share = {
  vkontakte: function(purl, ptitle, pimg, text) {
    url  = 'http://vkontakte.ru/share.php?';
    url += 'url='          + encodeURIComponent(purl);
    url += '&title='       + encodeURIComponent(ptitle);
    url += '&description=' + encodeURIComponent(text);
    url += '&image='       + encodeURIComponent(pimg);
    url += '&noparse=true';
    Share.popup(url);
  },
  odnoklassniki: function(purl, text) {
    url  = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
    url += '&st.comments=' + encodeURIComponent(text);
    url += '&st._surl='    + encodeURIComponent(purl);
    Share.popup(url);
  },
  facebook: function(purl, ptitle, pimg, text) {
    url  = 'http://www.facebook.com/sharer.php?s=100';
    url += '&p[title]='     + encodeURIComponent(ptitle);
    url += '&p[summary]='   + encodeURIComponent(text);
    url += '&p[url]='       + encodeURIComponent(purl);
    url += '&p[images][0]=' + encodeURIComponent(pimg);
    Share.popup(url);
  },
  twitter: function(purl, ptitle) {
    url  = 'http://twitter.com/share?';
    url += 'text='      + encodeURIComponent(ptitle);
    url += '&url='      + encodeURIComponent(purl);
    url += '&counturl=' + encodeURIComponent(purl);
    Share.popup(url);
  },
  mailru: function(purl, ptitle, pimg, text) {
    url  = 'http://connect.mail.ru/share?';
    url += 'url='          + encodeURIComponent(purl);
    url += '&title='       + encodeURIComponent(ptitle);
    url += '&description=' + encodeURIComponent(text);
    url += '&imageurl='    + encodeURIComponent(pimg);
    Share.popup(url)
  },

  popup: function(url) {
    window.open(url,'','toolbar=0,status=0,width=626,height=436');
  }
};

Social = {
		facebook: function(purl, ptitle, pimg, text) {			
			url  = 'http://www.facebook.com/sharer.php?s=100';
			url += '&p[title]='     + encodeURIComponent(ptitle);
			url += '&p[summary]='   + encodeURIComponent(text);
			url += '&p[url]='       + encodeURIComponent(purl);
			url += '&p[images][0]=' + pimg;
			Social.popup(url)

	      var head = document.getElementsByTagName('head')[0];
			var elem = document.createElement('script');
	        elem.src = 'http://graph.facebook.com/?callback=Facebook.Share.count&ids=' + encodeURIComponent(purl);
	        head.appendChild(elem);
			return false;
		},
		vkontakte : function(purl, ptitle, pimg, text) {
			url  = 'http://vk.com/share.php?';
			url += 'url='          + encodeURIComponent(purl);
			url += '&title='       + encodeURIComponent(ptitle);
			url += '&description=' + encodeURIComponent(text);
			url += '&image='       + pimg;
			url += '&noparse=false';
        Social.popup(url)

	      var head = document.getElementsByTagName('head')[0];
			var elem = document.createElement('script');
	        elem.src = 'http://vk.com/share.php?act=count&index=0&url=' + encodeURIComponent(purl);
	        head.appendChild(elem);
			return false;
		},
		twitter :function(purl, ptitle) {
			url  = 'http://twitter.com/share?';
			url += 'text='      + encodeURIComponent(ptitle);
			url += '&url='      + encodeURIComponent(purl);
			url += '&counturl=' + encodeURIComponent(purl);
        Social.popup(url)

	      var head = document.getElementsByTagName('head')[0];
			var elem = document.createElement('script');
	        elem.src = 'http://urls.api.twitter.com/1/urls/count.json?callback=Twitter.Share.count&url=' + encodeURIComponent(url);
	        head.appendChild(elem);

			return false;
		},
		instagram : function(){
        Social.popup(url)
			return false;
		},
		punit : function(purl, pimg, ptitle){
			url  = 'http://pinterest.com/pin/create/button/?';
			url += 'url='          + encodeURIComponent(purl);
			url += '&media='       + pimg;
			url += '&description=' + encodeURIComponent(ptitle);
        Social.popup(url)

	      var head = document.getElementsByTagName('head')[0];
			var elem = document.createElement('script');
	        elem.src = 'http://api.pinterest.com/v1/urls/count.json?callback=Punit.Share.count&url=' + encodeURIComponent(url);
	        head.appendChild(elem);

			return false;
		},
		googleplus : function(purl){
			url = 'https://plus.google.com/share?url=' + encodeURIComponent(purl);
         Social.popup(url)
	      var head = document.getElementsByTagName('head')[0];
			var elem = document.createElement('script');
	        elem.src = 'http://share.yandex.ru/gpp.xml?url=' + encodeURIComponent(url);
	        head.appendChild(elem);

			return false;
		},
		popup: function(url) {
			window.open(url,'','toolbar=0,status=0,width=626,height=436');
			return false;
		},
		initCountes: function(url){

			//ВК
	      var head = document.getElementsByTagName('head')[0];
			var elem = document.createElement('script');
	        elem.src = 'http://vk.com/share.php?act=count&index=0&url=' + encodeURIComponent(url);
	        head.appendChild(elem);
			var elem = document.createElement('script');
	        elem.src = 'http://graph.facebook.com/?callback=Facebook.Share.count&ids=' + encodeURIComponent(url);
	        head.appendChild(elem);
			var elem = document.createElement('script');
	        elem.src = 'http://urls.api.twitter.com/1/urls/count.json?callback=Twitter.Share.count&url=' + encodeURIComponent(url);
	        head.appendChild(elem);

			var elem = document.createElement('script');
	        elem.src = 'http://api.pinterest.com/v1/urls/count.json?callback=Punit.Share.count&url=' + encodeURIComponent(url);
	        head.appendChild(elem);
			var elem = document.createElement('script');
	        elem.src = 'http://share.yandex.ru/gpp.xml?url=' + encodeURIComponent(url);
	        head.appendChild(elem);

		}

}
// ВК
if (!window.VK) window.VK = {};
if (!VK.Share) {
		VK.Share =  {
				count: function($el,$count){
						jQuery('#vk-count').html($count);
				}
		}
}

// Facebook
if (!window.Facebook) window.Facebook = {};
if (!Facebook.Share) {
		Facebook.Share =  {
				count: function($el){
						var $cntr = 0;
						jQuery.each($el,function(){
							if(this.shares) $cntr=this.shares;
						});
						jQuery('#facebook-count').html($cntr);
				}
		}
}

// Twitter
if (!window.Twitter) window.Twitter = {};
if (!Twitter.Share) {
		Twitter.Share =  {
				count: function($el){
						var $cntr = 0;
						if ($el.count) $cntr = $el.count;
						jQuery('#twitter-count').html($cntr);
				}
		}
}

// Punit
if (!window.Punit) window.Punit = {};
if (!Punit.Share) {
		Punit.Share =  {
				count: function($el){
						console.log('Punit');
						var $cntr = 0;
						if ($el.count) $cntr = $el.count;
						jQuery('#punit-count').html($cntr);
				}
		}
}


// G+
if(!window.services){
			window.services={};
			window.services.gplus={}
}
window.services.gplus.cb=function($cntr){
	jQuery('#gplus-count').html($cntr);
};