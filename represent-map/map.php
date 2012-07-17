<?php
include "../header.php";
$places = mysql_query("SELECT * FROM places WHERE approved='1' ORDER BY title");
?>

var map;
var infowindow = null;
var gmarkers = [];
var markerTitles =[];
var highestZIndex = 0;  
var agent = "default";
var zoomControl = true;


// detect browser agent
$(document).ready(function(){
  if(navigator.userAgent.toLowerCase().indexOf("iphone") > -1 || navigator.userAgent.toLowerCase().indexOf("ipod") > -1) {
    agent = "iphone";
    zoomControl = false;
  }
  if(navigator.userAgent.toLowerCase().indexOf("ipad") > -1) {
    agent = "ipad";
    zoomControl = false;
  }
}); 


function initialize() {
  // set map styles
  var mapStyles = [
    {
      featureType: "administrative.land_parcel",
      stylers: [
        { visibility: "off" }
      ]
    },{
      featureType: "water",
      stylers: [
        { visibility: "on" },
        { saturation: 31 },
        { lightness: 39 }
      ]
    },{
      featureType: "road.highway",
      stylers: [
        { visibility: "simplified" },
        { lightness: 18 }
      ]
    }
  ];

  // set map options
  var myOptions = {
    zoom: 12,
    //minZoom: 10,
    center: new google.maps.LatLng(34.034453,-118.341293),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    panControl: false,
    streetViewControl: false,
    mapTypeControl: false,
    zoomControl: zoomControl,
    styles: mapStyles,
    zoomControlOptions: {
      style: google.maps.ZoomControlStyle.SMALL,
      position: google.maps.ControlPosition.TOP_LEFT
    }
  };
  map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
  zoomLevel = map.getZoom();

  // prepare infowindow
  infowindow = new google.maps.InfoWindow({
    content: "holding..."
  });

  // only show marker labels if zoomed in
  google.maps.event.addListener(map, 'zoom_changed', function() {
    zoomLevel = map.getZoom();
    if(zoomLevel <= 15) {
      $(".marker_label").css("display", "none");
    } else {
      $(".marker_label").css("display", "inline");
    }
  });

  // markers array: name, type (icon), lat, long, description, uri, address
  markers = new Array();
  <?php
    $marker_id = 0;
    while($place = mysql_fetch_assoc($places)) {
      $place[title] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[title])));
      $place[description] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[description])));
      $place[uri] = addslashes(htmlspecialchars($place[uri]));
      $place[address] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[address])));
      echo "
        markers.push(['".$place[title]."', '".$place[type]."', '".$place[lat]."', '".$place[lng]."', '".$place[description]."', '".$place[uri]."', '".$place[address]."']); 
        markerTitles[".$marker_id."] = '".$place[title]."';
      "; 
      $count[$place[type]]++;
      $marker_id++;
    }
  ?>

  // add markers
  jQuery.each(markers, function(i, val) {
    infowindow = new google.maps.InfoWindow({
      content: ""
    });

    // offset latlong ever so slightly to prevent marker overlap
    rand_x = Math.random();
    rand_y = Math.random();
    val[2] = parseFloat(val[2]) + parseFloat(parseFloat(rand_x) / 6000);
    val[3] = parseFloat(val[3]) + parseFloat(parseFloat(rand_y) / 6000);

    // show smaller marker icons on mobile
    if(agent == "iphone") {
      var iconSize = new google.maps.Size(16,19);
    } else {
      iconSize = null;
      var iconSize = new google.maps.Size(24,28);
    }

    // build this marker
    var markerImage = new google.maps.MarkerImage("./images/icons/"+val[1]+".png", null, null, null, iconSize);
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(val[2],val[3]),
      map: map,
      title: '',
      clickable: true,
      infoWindowHtml: '',
      zIndex: 10 + i,
      icon: markerImage
    });
    marker.type = val[1];
    gmarkers.push(marker);

    // add marker hover events (if not viewing on mobile)
    if(agent == "default") {
      google.maps.event.addListener(marker, "mouseover", function() {
        this.old_ZIndex = this.getZIndex(); 
        this.setZIndex(9999); 
        $("#marker"+i).css("display", "inline");
        $("#marker"+i).css("z-index", "99999");
      });
      google.maps.event.addListener(marker, "mouseout", function() { 
        if (this.old_ZIndex && zoomLevel <= 15) {
          this.setZIndex(this.old_ZIndex); 
          $("#marker"+i).css("display", "none");
        }
      }); 
    }

    // format marker URI for display and linking
    var markerURI = val[5];
    if(markerURI.substr(0,7) != "http://") {
      markerURI = "http://" + markerURI; 
    }
    var markerURI_short = markerURI.replace("http://", "");
    var markerURI_short = markerURI_short.replace("www.", "");

    // add marker click effects (open infowindow)
    google.maps.event.addListener(marker, 'click', function () {
      infowindow.setContent(
        "<div class='marker_title'>"+val[0]+"</div>"
        + "<div class='marker_uri'><a target='_blank' href='"+markerURI+"'>"+markerURI_short+"</a></div>"
        + "<div class='marker_desc'>"+val[4]+"</div>"
        + "<div class='marker_address'>"+val[6]+"</div>"
      );
      infowindow.open(map, this);
    });

    // add marker label
    var latLng = new google.maps.LatLng(val[2], val[3]);
    var label = new Label({
      map: map,
      id: i
    });
    label.bindTo('position', marker);
    label.set("text", val[0]);
    label.bindTo('visible', marker);
    label.bindTo('clickable', marker);
    label.bindTo('zIndex', marker);
  });


  // zoom to marker if selected in search typeahead list
  $('#search').typeahead({
    source: markerTitles, 
    onselect: function(obj) {
      marker_id = jQuery.inArray(obj, markerTitles);
      if(marker_id) {
        map.panTo(gmarkers[marker_id].getPosition());
        map.setZoom(15);
        google.maps.event.trigger(gmarkers[marker_id], 'click');
      }
      $("#search").val("");
    }
  });

} 


// zoom to specific marker
function goToMarker(marker_id) {
  if(marker_id) {
    map.panTo(gmarkers[marker_id].getPosition());
    map.setZoom(15);
    google.maps.event.trigger(gmarkers[marker_id], 'click');
  }
}

// toggle (hide/show) markers of a given type
function toggle(type) {
  if($("#filter_"+type).attr('checked') == "checked") {
    show(type); 
  } else {
    hide(type); 
  }
}

// hide all markers of a given type
function hide(type) {
  for (var i=0; i<gmarkers.length; i++) {
    if (gmarkers[i].type == type) {
      gmarkers[i].setVisible(false);
      $("#list .list-items > li."+type).css("display", "none");
    }
  }
}

// show all markers of a given type
function show(type) {
  for (var i=0; i<gmarkers.length; i++) {
    if (gmarkers[i].type == type) {
      gmarkers[i].setVisible(true);
      $("#list .list-items > li."+type).css("display", "block");
    }
  }
}

// toggle collapsible list
function toggleList() {
  if($("#list").css("display") == "none") {
    $("#list").css("display", "block"); 
    $(".menu").addClass("expanded");
    $("#list-toggle-button").html("Close List &#187;");
    $("#list-toggle-button").blur();
    $(".share").css("display", "block"); 
  } else {
    $("#list").css("display", "none"); 
    $(".menu").removeClass("expanded");
    $("#list-toggle-button").html("&#171; Open List");
    $("#list-toggle-button").blur();
  }
}

// hover on list item
function markerListMouseOver(marker_id) {
  $("#marker"+marker_id).css("display", "inline");
}
function markerListMouseOut(marker_id) {
  $("#marker"+marker_id).css("display", "none");
}

google.maps.event.addDomListener(window, 'load', initialize);