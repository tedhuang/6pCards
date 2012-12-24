 

//google.load("visualization", "1", {packages:["corechart"]});
//google.setOnLoadCallback(drawChart);



$("document").ready(function(){
	console.log(getScoreData());
})


function drawChart() {
	  
      var data =  getScoreData();
     
      
	  var options = {
	    title: 'Score Graph',
	    colors: ['red','blue'],
	    chartArea: {left: 25, top: 20, width: "85%", height: "85%"},
	    legend: {position: 'bottom', textStyle: {color: 'black', fontSize: 12}},
	    hAxis: {gridlines:{count: score_count}},
	    vAxis: {gridlines:{count: 8}}
	  };
	
	  
	 
	  var chart = new google.visualization.LineChart(document.getElementById('score_graph'));
	  chart.draw(data, options);
}