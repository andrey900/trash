
function checkForm(formData, jqForm, options)
{

	var pattern = /[a-z0-9][a-z0-9-\.]+\@[a-z0-9][a-z0-9-\.]+\.[a-z]+/;
	var pattern2 = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,4}$/;

    var mess = '';
        $(jqForm).find('.req').find('input,textarea,select').each(function(e)
		{
        	if($(this).val() == '' )
			{
            	$(this).css('border','solid #FF0000 2px');
                mess = 'Заполните все обязательные поля';
			}
			else
				if($(this).attr('name') == 'email' && !pattern2.exec(""+$(this).val()))
				{
	            	$(this).css('border','solid #FF0000 2px');
	                mess = 'Заполните поле email верно';
				}
				else
				{
	               	$(this).css('border','');
				}
		});

 	  if(mess!='')
	  {
	  	 alert(mess);
      	return false;
	  }
	  else
	  	return true;

}
// post-submit callback
function showResponse(responseText, statusText, xhr, $form)  {
      $form.replaceWith(responseText);
}