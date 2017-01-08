<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true&v=3.exp&amp;sensor=false"></script>
<?

$curort_inf_mas = array(
    0 => array('ID' => 168,'NAME' => 'Отель Der Steirerhof, 5*','PROPERTY_MAP_X_VALUE' => '47.16807','PROPERTY_MAP_Y_VALUE' => '16.030901'),
    1 => array('ID' => 335,'NAME' => 'Комплекс Heiltherme','PROPERTY_MAP_X_VALUE' => '47.168477','PROPERTY_MAP_Y_VALUE' => '16.026505'),
    2 => array('ID' => 336,'NAME' => 'Семейный аквапарк H20','PROPERTY_MAP_X_VALUE' => '47.177615','PROPERTY_MAP_Y_VALUE' => '16.001118')
);
?>
<?php //p($curort_inf_mas);?>
    <div class="s_map">
        <div id="s_map" style="height:300px;width:300px;">

        </div>
    </div>
    <div class="map_block">
        <a href="#" class="see_map"><div id="show_map_text">Показать карту</div></a>

        <script type="text/javascript">

            var geocoder;
            var map;
            var marker;
            var markers = Array();
            var bounds = new google.maps.LatLngBounds(); 
            function initialize(){
                //Определение карты
                var latlng = new google.maps.LatLng(55.796854, 37.553201);
                var options = {
                    zoom: 12,
                    mapTypeControl: false,
                    //center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(document.getElementById("s_map"), options);

                //Определение геокодера
                geocoder = new google.maps.Geocoder();


                marker = new google.maps.Marker({
                    map: map,
                    draggable: true
                });

            }
            function codeAddress(adress_name, title) {
                //var address = document.getElementById("address").value;
                var address = adress_name;
                geocoder.geocode( { 'address': address}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setCenter(results[0].geometry.location);

                        var contentString = '<div id="content"><b>'+title+'</b></div>';
                        var infowindow = new google.maps.InfoWindow({
                            content: contentString
                        });

                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: title
                        });
                        markers.push(marker);

                        google.maps.event.addListener(marker, 'click', function() {
                            infowindow.open(map,marker);
                        });
                    }
                });
            }       

            function coord(x,y, title) {    
                var latlng = new google.maps.LatLng(x,y);

                var contentString = '<div id="content"><b>'+title+'</b></div>';
                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                var marker = new google.maps.Marker({
                    map: map,
                    position: latlng,
                    title:title
                });
                markers.push(marker);

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });
            }                                


/*            $('.map_block .see_map').toggle(function(){
                $('#s_map').css({height:300+'px'});
                $('#s_map').slideDown(function(){*/
                    initialize();

                    <? foreach($curort_inf_mas as $curort_inf_data_coord){?>
                        <? if($curort_inf_data_coord["PROPERTY_MAP_ADRESS_VALUE"] || ($curort_inf_data_coord["PROPERTY_MAP_X_VALUE"] && $curort_inf_data_coord["PROPERTY_MAP_Y_VALUE"])){?>
                            <? if($curort_inf_data_coord["PROPERTY_MAP_ADRESS_VALUE"]){?>
                                codeAddress("<?=$curort_inf_data_coord["PROPERTY_MAP_ADRESS_VALUE"]?>", "<?=$curort_inf_data_coord["NAME"]?>");    
                                <? }else{?>
                                coord("<?=$curort_inf_data_coord["PROPERTY_MAP_X_VALUE"]?>", "<?=$curort_inf_data_coord["PROPERTY_MAP_Y_VALUE"]?>", "<?=$curort_inf_data_coord["NAME"]?>");
                                <? }?>    
                            <? }?>
                        <? }?>
                    for(var i in markers)
                    {
                        bounds.extend(markers[i].getPosition());
                    }
                    map.setCenter(bounds.getCenter());
                    map.fitBounds(bounds);
//                });
                document.getElementById('show_map_text').innerHTML = 'Скрыть карту';
/*                },function(){
                    $('#s_map').slideUp();
                    document.getElementById('s_map').innerHTML = '';
                    document.getElementById('show_map_text').innerHTML = 'Показать карту';
            });*/

        </script>
        <div class="clear"></div>
    </div>