<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
<title>Human Data</title>

<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 60% }
	  #tableData td {width: 300px;}
    </style>

    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=&sensor=false">
    </script>

    <script type="text/javascript">
	    var map;
      
		function setMarker(point, nextLa, nextLo) {
			var la = point["la"];
			var lo = point["lo"];
			var la_lo = new google.maps.LatLng(la, lo);
			var marker = new google.maps.Marker({
				position: la_lo, 
				map: map, 
				icon: {
		      		path: google.maps.SymbolPath.CIRCLE,
		      		scale: 4
		    	},
				title: point["date"] + ", " + point["mph"]
			});	
			
			if (nextLa != 0 && nextLo != 0) {
				var line = new google.maps.Polyline({
				    path: [new google.maps.LatLng(la, lo), new google.maps.LatLng(nextLa, nextLo)],
				    strokeColor: "#C0C0C0",
				    strokeOpacity: 1.0,
				    strokeWeight: 4,
				    map: map,
				});				
			}
		}
      
    </script>

</head>
<body>
	<div id="map-canvas"></div>
	<div>
	<!--Reference: <a href="https://developer.apple.com/library/ios/documentation/CoreLocation/Reference/CLLocation_Class/CLLocation/CLLocation.html">CLLocation</a>-->
</div>

<div>
	<?php
	//"1397789123"
	$files = array("1397789198", "1397790699", "1397790879", "1397794120");
	
	date_default_timezone_set('America/Los_Angeles');
	echo "<table id='tableData'>";
	echo "<tr><td>Time (America/Los_Angeles)</td><td>Coordinate (latitude, longitude)</td><td>Speed (mph)</td></tr>";
	$newData = array();
	
	foreach ($files as $filename) {
		$json = file_get_contents($filename.".json");
		//echo $json;
		$data = json_decode($json, true);	
		//print_r($data);

		$locationData = $data["location_data"];
		//print_r($locationData);
		
		foreach ($locationData as $dataPoint) {
			$mph = metersPerSecToMPH($dataPoint["sp"]);
			
			echo "<tr>";
			
			$dateStr = date('l jS \of F Y h:i:s A', $dataPoint["da"]);
			
			$la = $dataPoint["la"];
			$lo = $dataPoint["lo"];
			
			$point = array();
			$point["date"] = $dateStr;
			$point["mph"] = $mph;
			$point["la"] = $la;
			$point["lo"] = $lo;
			
			echo "<td>".$dateStr."</td><td>".$la.", ".$lo."</td><td>".$mph."</td>";
			echo "</tr>";
			$newData[] = $point;
			
		} // end foreach

		$newData_js = json_encode($newData);
		//echo $newData_js;		
	}
	echo "</table>";

	?>
	</div>
	
	<script type="text/javascript">
	
		function initialize() {

      		var mapOptions = {
        		center: new google.maps.LatLng(37.76696, -122.411),
        		zoom: 16
      		};

      		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

			var points = <?php echo $newData_js; ?>;

			for (var i=0; i<points.length; i++){ 
				var point = points[i];

				var nextLa = 0;
				var nextLo = 0;
				if (i < points.length-1) {
					nextLa = points[i+1]["la"];
					nextLo = points[i+1]["lo"];
				}
			
				setMarker(point, nextLa, nextLo);
			}
    	}

		google.maps.event.addDomListener(window, 'load', initialize);
	</script>
	
	<?php
	function metersPerSecToMPH($mps) {
		return 2.23695*$mps;
	}
	
	?>
</body>
</html>