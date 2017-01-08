<?CJSCore::Init(array("jquery"));?>
<script type="">
    $(document).ready(function(){
        $('#section_select_button').on('click', function(){
            var sect = $('#section_select').val();
            $.get('/include/get_action_data.php', {'mode':'brands', 'values':sect}, function(data){
                if(data){
                    $('#brands_select').html(data);
                    $('#brands_select').attr('disabled', false);
                }
                $('#brand_select_button').attr('disabled', false); 
                $('#brand_select_button_news').attr('disabled', false); 
            });
            return false;
        });   
        $('#brand_select_button').on('click', function(){
            var sect = $('#section_select').val();
            var brand = $('#brands_select').val();
            $.get('/include/get_action_data.php', {'mode':'elements', 'values':sect, 'brands':brand}, function(data){
                if(data){
                    $('ul.action_elements_list').html(data);
                }
            });
            return false;
        });           
        $('#brand_select_button_news').on('click', function(){
            var sect = $('#section_select').val();
            var brand = $('#brands_select').val();
            $.get('/include/get_action_data.php', {'mode':'elements_news', 'values':sect, 'brands':brand}, function(data){
                if(data){
                    $('ul.action_elements_list').html(data);
                }
            });
            return false;
        });    
        $('#elements_select_button').on('click', function(){
            $('.action_elements_list input').attr('checked', true);
            return false;
        });               
    });
</script>
<style type="">    
.action_elements_list li {
    display: inline-block;
    list-style-type: none;
    width: 33%;
}
</style>