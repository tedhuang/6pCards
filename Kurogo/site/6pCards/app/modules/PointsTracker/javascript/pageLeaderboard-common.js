var chart = null;

$(document).ready(function(){
	
	$(".show-chart").click(function(){
		if($("#chart").is(':visible')){
			$(".show-chart").html("Show Graph");
			
			$("#chart").slideUp(1500, "jswing", function(){});
		}
		else{
			$(".show-chart").html("Hide Graph");
			
			var height =  $(window).height() - 100;
			$("#chart svg").height( height);
			
			$("#chart").slideDown(1500, "jswing", function(){
				drawChart();
			});
			
			
		}
	});
	
	
	
});


function drawChart(){
	nv.addGraph(function() {
	    chart = nv.models.linePlusBarChart()
	          .margin({top: 30, right: 50, bottom: 50, left: 20})
	          .x(function(d,i) { return i })
	          .y(function(d) { return d[1] })
	          .color(d3.scale.category10().range());

	    chart.xAxis
	      .showMaxMin(false)
	      .tickFormat(function(d) {
	        var dx = data[0].values[d] && data[0].values[d][0] || 0;
	        return d3.time.format('%x')(new Date(dx))
	      });

	    chart.y1Axis
	        .tickFormat(d3.format(',f'));

	    chart.y2Axis
	        .tickFormat(function(d) { return d3.format('02d')(d*100) + "%" });

	    chart.bars.forceY([0]);

	    d3.select('#chart svg')
	        .datum(data)
	      .transition().duration(500).call(chart);

	    nv.utils.windowResize(chart.update);

	    return chart;
	});
}