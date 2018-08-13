<?php
/**
 * Created by PhpStorm.
 * User: ankit
 * Date: 3/7/2018
 * Time: 6:23 AM
 */
$latitude="0";
$longitude="0";


if(isset($_GET['parameter1']))
{
    if($_GET['radio'] == 'userLocation') {
        $latLng = getLatLng();
    }
    else
    {
        $latLng = $_GET['locationData'];
    }
    $nearbyPlacesJSON = nearbySearch($latLng);
    echo $nearbyPlacesJSON;
    exit;

}
if(isset($_GET['parameter2']) || isset($_GET['parameter4']))
{
    $reviews = getReviews($_GET['placeid']);
    echo $reviews;
    exit;
}
if(isset($_GET['parameter3']))
{
    $resultOfTransfer = getPhoto($_GET['parameter5'],$_GET['parameter6'],$_GET['parameter7']);
    echo $resultOfTransfer;
    exit;
}
if(isset($_GET['parameter8']))
{
    $latLngArray = getGeocodeLocation();
    echo json_encode($latLngArray);
    exit;

}

function getLatLng()
{
    $apiKey = "AIzaSyCACTfWpxgmHOqvb-yPNDjbftChitUP5cY";

    $Keyword = $_GET['keyword'];
    $Category = $_GET['categorySelected'];
    $Distance = $_GET['distance'];
    $Location = $_GET['locationData'];
    //$LocationRadius = $_GET['locationRadius'];
    $Distance = $Distance * 1609.344;

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $maps_url1 = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($Location) . '&API_KEY=' . urlencode($apiKey);
    // echo "Geocoding URL = ".$maps_url1. "<br/>";
    $maps_json1 = file_get_contents($maps_url1, false, stream_context_create($arrContextOptions));
    $maps_array1 = json_decode($maps_json1, true);

    $resultArray1 = $maps_array1['results'];
    $zerothElement = $resultArray1[0];
    $geometry = $zerothElement['geometry'];
    $location = $geometry['location'];
    $lat = $location['lat'];
    $lng = $location['lng'];
    global $latitude;
    global  $longitude;
    $latitude = $lat;
    $longitude = $lng;

    $latLng = $lat . ',' . $lng;
    //echo "latlng = " . $latLng."<br/>";

    return $latLng;
}

function nearbySearch($latLngValue){


    $Keyword = $_GET['keyword'];
    $Category = $_GET['categorySelected'];
    $Distance = $_GET['distance'];
    $Location = $_GET['radio'];
    // $LocationRadius = $_GET['locationRadius'];
    $Distance = $Distance * 1609.344;

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    //echo "step 1";
    //$maps_url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=41.8781136,-87.6297982&radius=16090&type=cafe&keyword=usc&key=AIzaSyCACTfWpxgmHOqvb-yPNDjbftChitUP5cY';
    $maps_url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='. urlencode($latLngValue). '&radius='. $Distance .'&type='. urlencode($Category) .'&keyword='. urlencode($Keyword) .'&key=AIzaSyCACTfWpxgmHOqvb-yPNDjbftChitUP5cY';
    //echo "nearbySearch map_url = ".$maps_url . "<br/>";
    //echo "step 2";
    $maps_json = file_get_contents($maps_url,false,stream_context_create($arrContextOptions)) or die("Unable to open URL2");
    //echo "step 3";
    $maps_array = json_decode($maps_json,true);

    $resultArray = $maps_array['results'];
    $arrayPlacesNearby = array();
    $index = 0;
    for($x = 0; $x < count($resultArray); $x++) {
        $res = $resultArray[$x];

        $arrayPlacesNearby[$index] = array();

        $arrayPlacesNearby[$index]['icon'] = $res['icon'];
        $arrayPlacesNearby[$index]['name'] = $res['name'];
        $arrayPlacesNearby[$index]['vicinity'] = $res['vicinity'];
        $arrayPlacesNearby[$index]['place_id'] = $res['place_id'];
        $arrayPlacesNearby[$index]['latitude'] = $GLOBALS['latitude'];
        $arrayPlacesNearby[$index]['longitude'] = $GLOBALS['longitude'];

        //$arrayPlacesNearby[$index]['place_id'] = array();


        $index++;
    }


    // return $arrayPlacesNearby;
    return json_encode($arrayPlacesNearby);
    // return $maps_json;

}

function getReviews($placeid)
{
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    $YOUR_API_KEY = 'AIzaSyBKPvdRoFqmuG5EBYcockzPBi_qFDwWsAM';
    $maps_url2 = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.$placeid.'&key='.$YOUR_API_KEY;
    $maps_json2 = file_get_contents($maps_url2,false,stream_context_create($arrContextOptions));
    $maps_array2 = json_decode($maps_json2,true);

    $resultArray2 = $maps_array2['result'];

    /* if(!(array_key_exists('photos',$resultArray2))) {
         $maps_json2 = "NO PHOTO";
     }
     if(array_key_exists('reviews',$resultArray2)) {
         $maps_json2 = "NO REVIEWS";
     }*/
    return $maps_json2;
}

function getPhoto($photoReference,$fileName,$placeIdForPhoto)
{
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    $fileName = $fileName.$placeIdForPhoto.'.png';
    $API_KEY = 'AIzaSyDwSiM90jntBVPh4OqMJcY8XIUOlmiMtdg';
    $maps_url2 = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=500&maxheight=500&photoreference='.$photoReference.'&key='.$API_KEY;
    $getContentResult = file_get_contents($maps_url2);
    //$resultOfTransfer = file_put_contents("/home/scf-14/ankithar/apache/apache2/htdocs/pics/img2.png",$maps_url2,FILE_USE_INCLUDE_PATH,stream_context_create($arrContextOptions));
    $resultOfTransfer = file_put_contents($fileName,$getContentResult);
    return $resultOfTransfer;
}


function getGeocodeLocation()
{
    $apiKey = "AIzaSyCACTfWpxgmHOqvb-yPNDjbftChitUP5cY";

    //$Keyword = $_GET['keyword'];
    //$Category = $_GET['categorySelected'];
    //$Distance = $_GET['distance'];
    $Location = $_GET['locationData'];
    //$LocationRadius = $_GET['locationRadius'];
    //$Distance = $Distance * 1609.344;

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $maps_url1 = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($Location) . '&API_KEY=' . urlencode($apiKey);
    // echo "Geocoding URL = ".$maps_url1. "<br/>";
    $maps_json1 = file_get_contents($maps_url1, false, stream_context_create($arrContextOptions));
    $maps_array1 = json_decode($maps_json1, true);

    $resultArray1 = $maps_array1['results'];
    $zerothElement = $resultArray1[0];
    $geometry = $zerothElement['geometry'];
    $location = $geometry['location'];
    $lat = $location['lat'];
    $lng = $location['lng'];

    $arr = array('latitude' => $lat, 'longitude' => $lng);
    return $arr;
    //echo "latlng = " . $latLng."<br/>";


}

?>

<html>
<head>
    <title>TestPlainPHP</title>
    <style>

        .heading{
            font-style: italic;
            font-family: sans;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0em;
            margin-top: 0em;
        }
        .boxed{
            background-color:rgb(245,245,245);
            border:1px solid #C8CFD6;
            margin-left: 25%;
            width:50%;
        }
        label{
            font-weight: bold;
        }
        .lineSpacing{
            margin-bottom: 0.5em;
            margin-top: 0.5em;
            margin-left: 0.5em;
        }
        .buttonStyle{
            border-radius: 15%;
            background-color: white;
            border: 1px solid #C8CFD6 ;
        }
        table {
            border-collapse: collapse;
            margin-top: 2%;
            margin-left: auto;
            margin-right: auto;
        }

        table, th, td {
            border: 1px solid #C8CFD6;
        }
        .arrowImage{
            width:30px;
            height:30px;
            margin-right: auto;
            margin-left: auto;

        }

        .reviewsTable{
            margin-left: 25%;
            width:50%;
        }

        .imagesTable{
            margin-left: 25%;
            width:50%;
        }

        .reviewImage{
            width:30px;
            height:30px;
        }

        .reviewPersonName{
            font-weight: bold;
            font-size: 1em;
        }

        .placeDetails{
            margin-left: 45%;
            margin-right: 45%;
            text-align: center;
        }
        #testMap {
            width: 100%;
            height: 400px;

        }

        .naviMap
        {
            width: 400px;
            height: 300px;
            position: absolute;
            top: 35px;
            left: 0;
            z-index: 5;
        }
        .dropList {
            z-index: 10;
            position: absolute;
            top: 35px;
            left: 0;

            background-color: #C8CFD6;
            padding: 5px;
            border: 1px solid #999;
            text-align: center;
            font-family: 'Roboto','sans-serif';
            line-height: 30px;
            padding-left: 10px;

            display:inline-block; vertical-align:top; overflow:hidden; border:solid grey 1px;
        }
        .show
        {
            visibility: visible;
        }
        .hide{
            visibility: hidden;
        }
        .selectClass { padding:5px; margin:-6px -20px -12px -11px; background-color: #C8CFD6;}

        .placeDetailButton
        {
            border: 0;
            background: transparent;
            cursor:pointer;
        }

        .myImg {

            cursor: pointer;

            padding: 5px 5px 5px 5px;
        }

        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 50px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }


    </style>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7AN-m3XqhR5Ep9Q0JiuyPoJr3Mi7QArw">
    </script>
    <script>
        function funcLatLng(info){
            latitudeJSONP = info.lat;
            longitudeJSONP = info.lon;
        }
    </script>
    <script type="text/javascript" src="http://ip-api.com/json/?callback=funcLatLng"></script>
</head>
<body>
<!--FORM-->
<form onsubmit="event.preventDefault(); onsubmitPreventDefault();">
    <div class="boxed">

        <h2 class="heading">Travel and Entertainment Search</h2>
        <hr width=97% color="#C8CFD6">
        <p class="lineSpacing"><label for="keyword">Keyword</label>
            <input type="text" name="keyword" id="keyword" required>
        </p>
        <p class="lineSpacing"><label for="category">Category</label>
            <select name="category" id="categorySelected">
                <option value="default" selected>default</option>
                <option value="cafe">cafe</option>
                <option value="bakery">bakery</option>
                <option value="restaurant">restaurant</option>
                <option value="beauty salon">beauty salon</option>
                <option value="casino">casino</option>
                <option value="movie theater">movie theater</option>
                <option value="lodging">lodging</option>
                <option value="airport">airport</option>
                <option value="train station">train station</option>
                <option value="subway station">subway station</option>
                <option value="bus station">bus station</option>
            </select>
        </p>
        <p class="lineSpacing"><label for="distance">Distance (miles)</label>
            <input type="text" name="distance" placeholder="10" id="distance"><span style="font-weight: bold;">from</span>
            <input type="radio" name="locationRadius" value="here" checked="checked" id="here" onclick="locationEnable1()"/>Here
        <p style="margin-left: 52%;" class="lineSpacing"><input type="radio" name="locationRadius" value="userLocation" id="userLocation" onclick="locationEnable2()"/><span><input type="text" name="location" id="location" placeholder="location" disabled required></span></p>
        </p>
        <p>
            <!--<input type="submit" id="search" name="search" value="Search" class="buttonStyle" disabled="disabled" style="margin-left: 20%;" onclick="getOutput1()"/>-->
            <input type="submit" id="search" name="search" value="Search" class="buttonStyle" disabled="disabled" style="margin-left: 20%;" onclick="getOutput1()"/>
            <input type="reset" name="clear" class="buttonStyle" value="Clear" onclick="clearFunc();"/>

        </p>

    </div>
</form>
<!--//FORM ENDS-->

<div id = "output1">

</div>
<div id="PlaceNameDiv" class="placeDetails" style="display:none;">
    <p style="font-size:15px;font-weight:bold;white-space:nowrap;" id="PlaceNamePara"></p>

</div>
<br/>
<div class="placeDetails" id="reviews" style="display:none;">
    <p style="font-size:13px;font-weight:bold;">click here for reviews</p>
    <button type="button" class="placeDetailButton" onclick="showReviews();"><img id="arrowReview" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" width="30px" height="20px"/></button>
    <!-- <button type="button" class="placeDetailButton"><img id="arrowReview" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" width="30px" height="20px"/></button>-->
    <!-- <a style="border:0px;background-color:white;"><img class="arrowImage" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png"/></a>-->
    <!-- <input type="button" class="myButtonDown"/>-->

</div>
<br/>
<div id="testReview" style="display:none;">

</div>
<br/>
<div class="placeDetails" id="photos" style="display:none;">
    <p style="font-size:13px;font-weight:bold;">click here for photos</p>
    <!--<button style="border:0px;background-color:white;"> <img class="arrowImage" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png"/></button>-->
    <!--<input type="button" class="myButtonUp"/>-->
    <button type="button" class="placeDetailButton" onclick="showPhotos();"><img id="arrowPhoto" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" width="30px" height="20px"/></button>
    <!--<button type="button" class="placeDetailButton"><img id="arrowPhoto" src="http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png" width="30px" height="20px"/></button>-->
</div>
<br/>
<div id="testPhoto" style="display:none;" class="imagesTable">

</div>

<div id="onclickID" hidden></div>

<div id="tdID" hidden></div>

<div id="a1" hidden></div>
<div id="a2" hidden></div>

<script type="text/javascript">

    var latitudeJSONP, longitudeJSONP;
    var divTd1,divTd2;
    var numOfPhotos;
    var imageArray = [];
    var latitudeCurrent,longitudeCurrent;

    window.onload = function () {
        document.getElementById("distance").defaultValue = 10;
        if(latitudeJSONP == null){
            latitudeJSONP = "34.0266";
            longitudeJSONP = "-118.2831";
        }
        else{
            document.getElementById("search").disabled = false;
        }

    }

    function clearFunc(){
        document.getElementById("output1").innerText = "";
        document.getElementById("PlaceNameDiv").style.display = "none";
        document.getElementById("reviews").style.display = "none";
        document.getElementById("testReview").innerText = "";
        document.getElementById("photos").style.display = "none";
        document.getElementById("testPhoto").innerText = "";
        location.reload();

    }

    function onsubmitPreventDefault(){
        getOutput1();
        return false;
    }

    function locationEnable1() {

        document.getElementById("location").disabled = true;

    }
    function locationEnable2() {

        document.getElementById("location").disabled = false;

    }

    function showReviews(){
        // alert("review " + document.getElementById("testReview").style.display);
        if(document.getElementById("testReview").style.display == 'none')
        {
            document.getElementById("arrowReview").setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png");
            document.getElementById("testReview").style.display = 'block';

        }
        else
        {
            document.getElementById("arrowReview").setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png");
            document.getElementById("testReview").style.display = 'none';
        }
    }

    function showPhotos(){
        // alert("photo " + document.getElementById("testPhoto").style.display);
        if(document.getElementById("testPhoto").style.display == 'none')
        {
            document.getElementById("arrowPhoto").setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png");
            document.getElementById("testPhoto").style.display = 'block';
        }
        else
        {
            document.getElementById("arrowPhoto").setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png");
            document.getElementById("testPhoto").style.display = 'none';
        }

    }

    function getOutput1() {
        var keyword = document.getElementById("keyword").value;
        var e = document.getElementById("categorySelected");
        var categorySelected = e.options[e.selectedIndex].value;
        var distance = document.getElementById("distance").value;

        var radio;
        if(document.getElementById("here").checked) {
            //TODO : use current lat lng
            radio = document.getElementById("here").value;
            locationData = latitudeJSONP + "," + longitudeJSONP;

        }
        else {
            radio = document.getElementById("userLocation").value;
            locationData = document.getElementById("location").value;
        }
        var elementID = "output1";

        if(keyword != "") {
            var url = "place.php?parameter1=getNearbyPlaces&keyword=" + keyword + "&categorySelected=" + categorySelected + "&distance=" + distance + "&radio=" + radio + "&locationData=" + locationData;
            //var url = "place.php?parameter1=getNearbyPlaces";
            //console.log("url = " + url);
            getRequest(
                url, // URL for the PHP file
                drawOutput,  // handle successful request
                drawError,    // handle error
                elementID
            );
        }
        // return false;
    }

    function parseNearbyPlacesJS(responseText) {

        //alert(responseText);
        var jsonObj = JSON.parse(responseText);
        if (jsonObj.length == 0) {
            var div = document.getElementById("output1");
            div.setAttribute("class", "boxed");
            div.style.textAlign = "center";
            div.style.fontWeight = "bold";
            div.innerText="No Record has been found";
        }

        else {
            var html_text = "<table>";
            html_text += "<tbody>";
            html_text += "<tr>";
            html_text += "<th>Category</th>";
            html_text += "<th>Name</th>";
            html_text += "<th>Address</th>";
            html_text += "</tr>";


            for (i = 0; i < jsonObj.length; i++) {

                html_text += "<tr>";
                //document.getElementById("JSONObj").innerText += jsonObj[i].icon+" "+jsonObj[i].name+" "+jsonObj[i].vicinity;
                //document.getElementById("JSONObj").innerHTML += "<br/>";
                html_text += "<td style=\"width:10%;\"><img src=" + jsonObj[i].icon + " style=\" display:block;width:40px; height:40px;\"" + " /></td>";
                //html_text+="<td style=\"width:45%;\"><a style=\"color:black;text-decoration:none;\" href=\"#\">"+jsonObj[i].name+"</a></td>";
                html_text += "<td id=\"" + jsonObj[i].place_id + "\" style=\"width:45%;\" onclick=\"getPlaceDetails(this.id)\"><a style=\"color:black;text-decoration:none;\" href=\"#\">" + jsonObj[i].name + "</a></td>";
                //+="<td id=\""+jsonObj[i].place_id+ "\" style=\"width:45%;\"><a style=\"color:black;text-decoration:none;\" href=\"#\">"+jsonObj[i].name+"</a></td>";
                html_text += "<td id=\"" + jsonObj[i].place_id + "\" style=\"width:45%;position:relative;\"><a id=\"" + i + "\" style=\"color:black;text-decoration:none;\" href=\"#\"  onclick=\"getPlaceMap(this)\">" + jsonObj[i].vicinity + "</a><div class=\"naviMap\" style=\"display:none;\"></div><div class=\"dropList\" style=\"display:none;\"></div></td>";
                // html_text+="<td id=\""+jsonObj[i].place_id+ "\" style=\"width:45%;\"><a id=\""+i+"\" style=\"color:black;text-decoration:none;\" href=\"#\"  onclick=\"getPlaceMap(this)\">"+jsonObj[i].vicinity+"</a><div class=\"naviMap\">abc</div><div class=\"dropList\">def</div></td>";
                html_text += "</tr>";
            }
            html_text += "</tbody>";
            html_text += "</table>";
            document.getElementById("output1").innerHTML = html_text;
            var lat = jsonObj[0].latitude;
            var lng = jsonObj[0].longitude;

            latitudeCurrent = lat;
            longitudeCurrent = lng;


            /*var nodeTd = document.getElementsByTagName("td");
            for(i=0;i<nodeTd.length;i++)
            {
                idTd = nodeTd[i].getAttribute("id");
                onclickVal = nodeTd[i].getAttribute("onclick");
                console.log("id "+i+" ="+idTd+" onclick = "+onclickVal);
            }*/

        }
    }

    function parsePlaceDetails(responseText) {


        getReviews(responseText);
        getPhotos(responseText);

    }

    function getReviews(responseText) {
        var jsonObj = JSON.parse(responseText);

        var result = jsonObj.result;
        //alert("Inside getReviews");

        var checkReview = false;
        var count = 0;
        Object.keys(result).forEach(function (k) {
            if (k == "reviews") {
                reviewResult = result.reviews;
                // console.log("REVIEWS review length = " + reviewResult.length);
                for (var m = 0; m < reviewResult.length; m++) {
                    if (reviewResult[m].text != "") {
                        count = count + 1;
                    }
                }
                //  alert("Setting checkReview to true");
                // console.log("REVIEWS COUNT = " + count);
                if (count > 0) {
                    checkReview = true;
                }

            }

        });


        if (checkReview == true) {

            var html_text = "<table class=\"reviewsTable\">";
            html_text += "<tbody>";

            reviewResult = result.reviews;
            var counter = 0;
            for (i = 0; i < reviewResult.length; i++) {
                if (counter != 5 && reviewResult[i].text != "") {
                    var reviewElement = reviewResult[i];
                    var authorPhoto = reviewElement.profile_photo_url;
                    var authorName = reviewElement.author_name;
                    var text = reviewElement.text;

                    html_text += "<tr>";
                    html_text += "<td class=\"reviewPersonName\"><div style=\"margin-left:40%;\"><img class=\"reviewImage\" src=\"" + authorPhoto + "\" style=\"display:inline;\" \/><p style=\"display:inline;\">" + authorName + "</p></td></tr></div>";
                    html_text += "<tr>";
                    html_text += "<td>" + text + "</td></tr>";
                    var authorPhoto = reviewElement.profile_photo_url;
                    var authorName = reviewElement.author_name;
                    var text = reviewElement.text;
                    // document.getElementById("testReview").innerText += authorPhoto + " " + authorName + " " + text + " " + "<br/><br/><br/>";
                    counter = counter + 1;
                }
                else {
                    break;
                }
            }

            html_text += "<tbody></table>";
            document.getElementById("testReview").innerHTML += html_text;

        }
        else {
            var div = document.getElementById("testReview");
            div.innerHTML = "No reviews found";
            div.setAttribute("class","boxed");
            div.style.fontWeight = "bold";
            div.style.textAlign = "center";
            div.style.backgroundColor = "#fff";
        }


    }


    function getPhotos(responseText) {
        var jsonObj = JSON.parse(responseText);

        var result = jsonObj.result;

        var placeIdForPhoto = result.place_id;
        // console.log("placeIdForPhoto = "+placeIdForPhoto);

        var checkPhotos = false;
        Object.keys(result).forEach(function (k) {
            if (k == "photos") {
                //  console.log("Setting checkPhoto to true");
                checkPhotos = true;
            }

        });
        var photoReferenceArray = [];
        if (checkPhotos == false) {

            var div = document.getElementById("testPhoto");
            div.innerHTML = "No photos found";
            div.setAttribute("class", "boxed");
            div.style.fontWeight = "bold";
            div.style.textAlign = "center";
            div.style.backgroundColor = "#fff";
        }
        else {
            var counter = 0;

            photoResult = result.photos;

            for (i = 0; i < photoResult.length; i++) {
                if (counter != 5) {
                    var photoElement = photoResult[i];
                    var photoref = photoElement.photo_reference;
                    //alert(photoref);
                    photoReferenceArray[counter] = photoref;
                    counter = counter + 1;
                }
                else {
                    break;
                }

            }
            // alert("array len = "+photoReferenceArray.length);
            for( var l=0;l<photoReferenceArray.length;l++)
            {
                //alert("calling get photo api for "+l);
                getOutputPhotos(photoReferenceArray[l],'testPhoto',l,placeIdForPhoto);
            }
            numOfPhotos = photoReferenceArray.length;
            // alert("Calling functionDisplayPhotos")

            preload(placeIdForPhoto);
            //functionDisplayPhotos(placeIdForPhoto);
            //  alert("returned from functionDisplayPhotos");


        }

    }

    function getPlaceDetails(placeID) {
        //alert("place id = "+placeID);
        var placeName = document.getElementById(placeID).innerText;

        var output1Element = document.getElementById("output1");
        output1Element.innerHTML="";


        //  alert("start make div visible");
        var v= document.getElementById("PlaceNamePara");
        v.innerText=placeName;
        var w = document.getElementById("PlaceNameDiv");
        w.style.display="block";
        var x = document.getElementById("reviews");
        x.style.display="block";
        var y = document.getElementById("photos");
        y.style.display="block";
        // alert("start make div invisible");

        // alert("Calling getReviewsAndPhotos");
        getOutputReviewsAndPhotos(placeID,"testReview");



    }


    //Get place details invocation of PHP

    function getOutputReviewsAndPhotos(placeid,elementID){
        var url = "place.php?parameter2=getReviewsAndPhotos&placeid="+placeid;
        //var url = "place.php?parameter1=getNearbyPlaces";
        // console.log("url = "+url);
        getRequest(
            url, // URL for the PHP file
            drawOutput,  // handle successful request
            drawError,    // handle error
            elementID
        );
        // return false;
    }


    //get photos
    function getOutputPhotos(photoReference,elementID,l,placeIdForPhoto){
        var url = "place.php?parameter3=getPhotos&parameter5="+photoReference+"&parameter6="+l+"&parameter7="+placeIdForPhoto;
        //var url = "place.php?parameter1=getNearbyPlaces";
        //  console.log("url = "+url);
        getRequest(
            url, // URL for the PHP file
            drawOutput,  // handle successful request
            drawError,    // handle error
            elementID
        );
        // return false;
    }

    // handles drawing an error message
    function drawError(elementID) {
        if(elementID == "output1")
        {
            var div = document.getElementById("output1");
            div.setAttribute("class", "boxed");
            div.style.textAlign = "center";
            div.style.fontWeight = "bold";
            div.innerText="No Record has been found";
        }
        else if(elementID == "testReview")
        {
            var div = document.getElementById("testReview");
            div.innerHTML = "No reviews found";
            div.setAttribute("class","boxed");
            div.style.fontWeight = "bold";
            div.style.textAlign = "center";
            div.style.backgroundColor = "#fff";
        }
        else if(elementID == "testPhoto")
        {
            var div = document.getElementById("testPhoto");
            div.innerHTML = "No photos found";
            div.setAttribute("class","boxed");
            div.style.fontWeight = "bold";
            div.style.textAlign = "center";
            div.style.backgroundColor = "#fff";
        }

        else{
            var container = document.getElementById(elementID);
            container.innerHTML = 'There was an error!';

        }

    }
    // handles the response, adds the html
    function drawOutput(responseText,elementID) {
        //alert("elementID="+elementID);
        if(elementID == "output1"){
            parseNearbyPlacesJS(responseText);
        }
        else if(elementID == "testReview"){
            //var container = document.getElementById(elementID);
            //container.innerHTML = responseText;
            parsePlaceDetails(responseText);
        }
        else if(elementID == "naviMap")
        {
            // alert(responseText);
            //  var container = document.getElementById(elementID);
            //container.innerHTML = responseText;
            mapManipulation(responseText);
        }
        else if(elementID == "testPhoto"){
            //alert("return from php getPhoto");
        }
        else
        {

            // alert(responseText);
        }



    }
    // helper function for cross-browser request object
    function getRequest(url, success, error, elementID) {
        var req = false;
        try {
            // most browsers
            req = new XMLHttpRequest();
        } catch (e) {
            // IE
            try {
                req = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                // try an older version
                try {
                    req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    //return false;
                }
            }
        }
        //if (!req) return false;
        if (typeof success != 'function') success = function () {
        };
        if (typeof error != 'function') error = function () {
        };
        req.onreadystatechange = function () {
            if (req.readyState == 4) {
                // return req.status === 200 ?
                //   success(req.responseText) : error(req.status);
                if (req.status == 200) {
                    success(req.responseText,elementID);
                }
                else {
                    error(req.status,elementID);
                }
            }
        }
        req.open("GET", url, true);
        req.send(null);
        // return req;
    }

    function getPlaceMap(sender) {

        var tr = sender.parentNode;
        var placeid = tr.getAttribute("id");

        // console.log("senderId = "+sender.id);
        //  console.log("palceid = "+placeid);
        document.getElementById("tdID").innerText = placeid;
        document.getElementById("onclickID").innerText = sender.id;

        var e1 = sender.parentNode;
        var nodeList = e1.childNodes;

        var check = "true";
        for(i=0;i<nodeList.length;i++)
        {
            if(nodeList[i].nodeType == 1)
            {
                if(nodeList[i].tagName == "DIV") {
                    if (check === "true") {
                        divTd1 = nodeList[i];
                        //  console.log("divTd1 = " + divTd1.getAttribute("class")+" style= "+divTd1.style.display);
                        check = "false";

                    }
                    else {
                        divTd2 = nodeList[i];
                        //  console.log("divTd2 = " + divTd2.getAttribute("class")+" style = "+divTd2.style.display);
                    }
                }
            }
        }

        if(divTd1.style.display == "none" && divTd2.style.display == "none")
        {

            divTd1.style.display = "block";
            divTd2.style.display = "block";
            getMapAPI(placeid,"naviMap");
        }
        else
        {

            divTd1.innerHTML='';
            divTd2.innerHTML='';
            divTd1.style.display = "none";
            divTd2.style.display = "none";
            return;
        }




    }

    function getMapAPI(placeid,elementID){
        var url = "place.php?parameter4=getMap&placeid="+placeid;
        //var url = "place.php?parameter1=getNearbyPlaces";
        //  console.log("url = "+url);
        getRequest(
            url, // URL for the PHP file
            drawOutput,  // handle successful request
            drawError,    // handle error
            elementID
        );
        // return false;
    }


    function mapManipulation(responseText)
    {
        /* var jsonObj = JSON.parse(responseText);
         var lat1 = jsonObj["result"]["geometry"]["location"]["lat"];
         var lng1 = jsonObj["result"]["geometry"]["location"]["lng"];
         var uluru = {lat: lat1, lng: lng1};
         var map = new google.maps.Map(document.getElementById('testMap'), {
             zoom: 12,
             center: uluru
         });
         var marker = new google.maps.Marker({
             position: uluru,
             map: map
         });

         alert(lat1 + " "+lng1);*/


        //divTd1.style.display = "block";
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;
        var jsonObj = JSON.parse(responseText);
        var lat1 = jsonObj["result"]["geometry"]["location"]["lat"];
        var lng1 = jsonObj["result"]["geometry"]["location"]["lng"];
        var uluru = {lat: lat1, lng: lng1};


        /* if(document.getElementById("userLocation").checked)
         {
             alert("Entering location check");
             var locationData1 = document.getElementById("location").value;

             var request = new XMLHttpRequest();
             var response;
             var url1 = "place.php?parameter8=getLocationCoords&locationData=" + locationData1;
             if (request.readyState == 4) {
                 if (request.status == 200) {
                     response = request.responseText;
                     console.log("1 = "+JSON.parse(response));
                     alert("1 = "+JSON.parse(response));
                 }
                 else {
                     response = "error";
                     alert("2 = "+response);
                 }
             }
             request.open("GET", url1, true);
             request.send(null);
         }
         console.log("1 = "+JSON.parse(response));
         alert("1 = "+JSON.parse(response));*/

        var map = new google.maps.Map(divTd1, {
            zoom: 12,
            center: uluru
        });
        var marker = new google.maps.Marker({
            position: uluru,
            map: map
        });
        directionsDisplay.setMap(map);


        //divTd2.style.display = "block";


        //Create array of options to be added
        var arrayValue = ["DRIVING","WALKING","BICYCLING"];
        var arrayText = ["Driving","Walking","Bicycling"];

        //Create and append select list
        var selectList = document.createElement("select");
        selectList.id = "mode";
        selectList.size = "3";
        selectList.setAttribute("class","selectClass");
        divTd2.appendChild(selectList);

        //Create and append the options
        for (var i = 0; i < arrayValue.length; i++) {
            var option = document.createElement("option");
            option.value = arrayValue[i];
            option.text = arrayText[i];
            selectList.appendChild(option);
        }


        calculateAndDisplayRoute(directionsService, directionsDisplay,lat1,lng1);
        document.getElementById('mode').addEventListener('change', function() {
            marker.setMap(null);
            calculateAndDisplayRoute(directionsService, directionsDisplay,lat1,lng1);
        });

    }

    function calculateAndDisplayRoute(directionsService, directionsDisplay,lat1,lng1) {
        var selectedMode = document.getElementById('mode').value;

        var lt,ln;
        if(document.getElementById("userLocation").checked)
        {
            lt = latitudeCurrent;
            ln = longitudeCurrent;
        }
        else{
            lt = latitudeJSONP;
            ln = longitudeJSONP;
        }

        directionsService.route({
            origin: {lat: lt, lng: ln},  // Haight.
            destination: {lat: lat1, lng: lng1},  // Ocean Beach.
            // Note that Javascript allows us to access the constant
            // using square brackets and a string value as its
            // "property."
            travelMode: google.maps.TravelMode[selectedMode]
        }, function(response, status) {
            if (status == 'OK') {
                directionsDisplay.setDirections(response);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }

    function preload(placeIdForPhoto)
    {
        for(var j=0;j<imageArray.length;j++)
        {
            imageArray[j] = "";
        }
        //debugger;
        for (var k = 0; k < numOfPhotos; k++) {
            var fn = k + placeIdForPhoto + '.png';
            imageArray[k] =  new Image();
            imageArray[k].src = fn;
            console.log(k+" name= "+imageArray[k].src);

        }
        functionDisplayPhotos(placeIdForPhoto);
    }

    function functionDisplayPhotos(placeIdForPhoto) {



        var imageTable = document.createElement("table");
        // console.log("number of photos = " + numOfPhotos);
        for (var k = 0; k < numOfPhotos; k++) {

            var fn = k + placeIdForPhoto + '.png';

            //imageTable.setAttribute("class","imagesTable");
            var tr = document.createElement("tr");
            imageTable.appendChild(tr);
            var td = document.createElement("td");
            tr.appendChild(td);
            var anchor = document.createElement("a");
            anchor.setAttribute("href", fn);
            anchor.onclick = function () {
                window.open(this.href, 'mywin', 'left=20,top=20,width=1000,height=1000,toolbar=1,resizable=0');
                return false;
            }
            td.appendChild(anchor);
            var imgEle = document.createElement("img");
          //  imgEle.setAttribute("class","myImg");
            //imgEle.setAttribute("src", fn);

            //  console.log("check "+k+" "+imageArray[k].src);
            imgEle.src = imageArray[k].src;
            imgEle.width = "500";
            // imgEle.maxWidth = "650";
            //imgEle.maxHeight = "650";
            // imgEle.height = "200";

            anchor.appendChild(imgEle);

            var divEle = document.getElementById("testPhoto");
            divEle.appendChild(imageTable);
        }

    }


    //For reference only
    /*  function initMap() {
          var uluru = {lat: -25.363, lng: 131.044};
          var map = new google.maps.Map(document.getElementById('testMap'), {
              zoom: 4,
              center: uluru
          });
          var marker = new google.maps.Marker({
              position: uluru,
              map: map
          });
      }*/


</script>


</body>
</html>