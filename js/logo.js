$(function() {
	var canvas = $("#c");
	var canvasHeight;
	var canvasWidth;
	var ctx;
	var dt = 0.1;
	
	var pointCollection;
	
	function init() {
		updateCanvasDimensions();
		
		var g = [
		
new Point(116.4, 138.1, 8, 20.6, "#FCEA0E"), 
new Point(94.6, -2.8, 8, 20.4, "#009A9B"), 
new Point(70, 81.3, 8, 33.8, "#D60A51"), 
new Point(95.5, 125.1, 7, 10.9, "#D60A51"), 
new Point(78, 120.6, 5, 8.7, "#662382"), 
new Point(231.7, -44.9, 8, 20.8, "#E61C72"), 
new Point(84.2, -38.9, 7, 17.8, "#E84D1A"), 
new Point(92.9, -57, 6, 12.8, "#FFED00"), 
new Point(247.8, -24.7, 6, 7.2, "#1C1C1A"), 
new Point(81.4, -20.1, 6, 7.2, "#DDDC00"), 
new Point(66.9, -17.9, 5, 13.2, "#0091CF"), 
new Point(196.2, -51.2, 8, 8.5, "#CABB9F"), 
new Point(199, -78.9, 8, 13.2, "#0091CF"), 
new Point(247.2, -11, 5, 8.1, "#B2B2B2"), 
new Point(246.4, 5, 7, 12.2, "#B2B2B1"), 
new Point(219.2, -20.3, 7, 10.4, "#E84D1A"), 
new Point(184.8, -52.2, 7, 5.7, "#302683"), 
new Point(188.2, -63, 4, 6.8, "#E61C72"), 
new Point(52.4, 20.5, 8, 9.1, "#302683"), 
new Point(237.4, -15.6, 4, 14.6, "#36A9E0"), 
new Point(118.6, -55.1, 5, 26.6, "#878786"), 
new Point(225.1, 2.2, 3, 20.2, "#009760"), 
new Point(169.6, -61.1, 3, 13.2, "#009660"), 
new Point(123.1, -77.4, 3, 13.2, "#008FA7"), 
new Point(137.2, -87.1, 2, 9.1, "#BD6477"), 
new Point(216.1, -40.6, 4, 14.6, "#7D4E23"), 
new Point(211.2, -64.9, 3, 17.9, "#00405C"), 
new Point(117.3, -31.5, 4, 12.6, "#E61C72"), 
new Point(103.5, -26.2, 7, 12.6, "#51C3F1"), 
new Point(71.9, 26.2, 3, 12.6, "#51C3F1"), 
new Point(181.9, -80.4, 2, 16.9, "#51C3F1"), 
new Point(201.4, 80.2, 5, 7.4, "#FEFEFE"), 
new Point(95.7, 143.9, 6, 10, "#6F6F6E"), 
new Point(83.8, 139.8, 5, 8.5, "#E63229"), 
new Point(96.8, 31.8, 6, 8.5, "#E61C72"), 
new Point(52.9, 42.6, 6, 17.1, "#F9B233"), 
new Point(67.1, 1.3, 4, 20.4, "#006532"), 
new Point(83.8, 47.5, 3, 18.2, "#662382"), 
new Point(216.7, 72, 4, 14.1, "#94C11E"), 
new Point(213.5, 115, 8, 20.6, "#009EE3"), 
new Point(113.7, 113.5, 6, 11.9, "#1C1C1A"), 
new Point(75.2, 129, 4, 12.6, "#94C11E"), 
new Point(153.8, 155.8, 5, 5.5, "#E61C72"), 
new Point(151.8, 116.5, 7, 6.1, "#C6C6C5"), 
new Point(166.4, 118.9, 6, 10.4, "#94C11E"), 
new Point(154.2, 127.9, 5, 7.8, "#E61C72"), 
new Point(186.9, 113.7, 5, 14.1, "#1C70B7"), 
new Point(166.1, 143.9, 4, 16.7, "#3AA934"), 
new Point(135.7, 123.6, 4, 14.3, "#E84D1A"), 
new Point(49, 69.2, 5, 12.4, "#0091CF"), 
new Point(62.4, 115.4, 3, 12.4, "#E84D1A"), 
new Point(140, 145.4, 3, 16.7, "#28225B"), 
new Point(199.4, 95.7, 4, 12.4, "#F9B233"), 
new Point(149.8, -67.9, 3, 21.8, "#A2185B"), 
new Point(96.6, 105, 4, 17.2, "#1C70B7"), 
new Point(136.1, -50.6, 2, 12.4, "#009A9B"), 
new Point(92.9, 19, 2, 15, "#009660"), 
new Point(157.3, -87.1, 1, 12.4, "#94C11E"), 
new Point(189.9, 134.6, 3, 18.2, "#732383"), 
new Point(227.5, 83.2, 0, 22.6, "#A2185B"), 
new Point(209.3, 134.6, 2, 8.9, "#1C1C1B"), 
new Point(232.1, 104.2, 2, 11.8, "#1C1C1B"), 
new Point(236.1, 106.3, 1, 7.4, "#1C1C1A")

];
		
		gLength = g.length;
		for (var i = 0; i < gLength; i++) {
			g[i].curPos.x = (canvasWidth/2 - 180) + g[i].curPos.x;
			g[i].curPos.y = (800/2 - 65) + g[i].curPos.y;
			
			g[i].homePos.x = (canvasWidth/2 - 180) + g[i].originalPos.x;
			g[i].homePos.y = (800/2 - 65) + g[i].originalPos.y;
		};
		
		pointCollection = new PointCollection();
		pointCollection.points = g;
		
		initEventListeners();
		timeout();
	};
	
	function initEventListeners() {
		$(window).bind('resize', updateCanvasDimensions).bind('mousemove', onMove);
		
		canvas.get(0).ontouchmove = function(e) {
			e.preventDefault();
			onTouchMove(e);
		};
		
		canvas.get(0).ontouchstart = function(e) {
			e.preventDefault();
		};
	};
	
	function updateCanvasDimensions() {
		var height = ($(window).height() < 800) ? 800 : $(window).height();
		canvas.attr({height: height, width: $(window).width()});
		canvasWidth = canvas.width();
		canvasHeight = canvas.height();
		
		if (pointCollection) {
			var points = pointCollection.points;
			var pointsLength = points.length;
			for (var i = 0; i < pointsLength; i++) {
				points[i].homePos.x = (canvasWidth/2 - 180) + points[i].originalPos.x;
			};		
		};
		
		draw();
	};
	
	function onMove(e) {
		var offset = canvas.offset();
		if (pointCollection)
			pointCollection.mousePos.set(e.pageX-offset.left, e.pageY-offset.top);
	};
	
	function onTouchMove(e) {
		var offset = canvas.offset();
		if (pointCollection)
			pointCollection.mousePos.set(e.targetTouches[0].pageX-offset.left, e.targetTouches[0].pageY-offset.top);
	};
	
	function timeout() {
		draw();
		update();
		
		setTimeout(function() { timeout() }, 30);
	};
	
	function draw() {
		var tmpCanvas = canvas.get(0);

		if (tmpCanvas.getContext == null) {
			return; 
		};
		
		ctx = tmpCanvas.getContext('2d');
		ctx.clearRect(0, 0, canvasWidth, canvasHeight);
		
		if (pointCollection)
			pointCollection.draw();
	};
	
	function update() {		
		if (pointCollection)
			pointCollection.update();
	};
	
	function Vector(x, y, z) {
		this.x = x;
		this.y = y;
		this.z = z;
 
		this.addX = function(x) {
			this.x += x;
		};
		
		this.addY = function(y) {
			this.y += y;
		};
		
		this.addZ = function(z) {
			this.z += z;
		};
 
		this.set = function(x, y, z) {
			this.x = x; 
			this.y = y;
			this.z = z;
		};
	};
	
	function PointCollection() {
		this.mousePos = new Vector(0, 0);
		this.points = new Array();
		
		this.newPoint = function(x, y, z) {
			var point = new Point(x, y, z);
			this.points.push(point);
			return point;
		};
		
		this.update = function() {		
			var pointsLength = this.points.length;
			
			for (var i = 0; i < pointsLength; i++) {
				var point = this.points[i];
				
				if (point == null)
					continue;
				
				var dx = this.mousePos.x - point.curPos.x;
				var dy = this.mousePos.y - point.curPos.y;
				var dd = (dx * dx) + (dy * dy);
				var d = Math.sqrt(dd);
				
				if (d < 150) {
					point.targetPos.x = (this.mousePos.x < point.curPos.x) ? point.curPos.x - dx : point.curPos.x - dx;
					point.targetPos.y = (this.mousePos.y < point.curPos.y) ? point.curPos.y - dy : point.curPos.y - dy;
				} else {
					point.targetPos.x = point.homePos.x;
					point.targetPos.y = point.homePos.y;
				};
				
				point.update();
			};
		};
		
		this.draw = function() {
			var pointsLength = this.points.length;
			for (var i = 0; i < pointsLength; i++) {
				var point = this.points[i];
				
				if (point == null)
					continue;

				point.draw();
			};
		};
	};
	
	function Point(x, y, z, size, colour) {
		this.colour = colour;
		this.curPos = new Vector(x, y, z);
		this.friction = 0.8;
		this.homePos = new Vector(x, y, z);
		this.originalPos = new Vector(x, y, z);
		this.radius = size;
		this.size = size;
		this.springStrength = 0.1;
		this.targetPos = new Vector(x, y, z);
		this.velocity = new Vector(0.0, 0.0, 0.0);
		
		this.update = function() {
			var dx = this.targetPos.x - this.curPos.x;
			var ax = dx * this.springStrength;
			this.velocity.x += ax;
			this.velocity.x *= this.friction;
			this.curPos.x += this.velocity.x;
			
			var dy = this.targetPos.y - this.curPos.y;
			var ay = dy * this.springStrength;
			this.velocity.y += ay;
			this.velocity.y *= this.friction;
			this.curPos.y += this.velocity.y;
			
			var dox = this.homePos.x - this.curPos.x;
			var doy = this.homePos.y - this.curPos.y;
			var dd = (dox * dox) + (doy * doy);
			var d = Math.sqrt(dd);
			
			this.targetPos.z = d/100 + 1;
			var dz = this.targetPos.z - this.curPos.z;
			var az = dz * this.springStrength;
			this.velocity.z += az;
			this.velocity.z *= this.friction;
			this.curPos.z += this.velocity.z;
			
			this.radius = this.size*this.curPos.z;
			if (this.radius < 1) this.radius = 1;
		};
		
		this.draw = function() {
			var x = this.curPos.x;
			var y = this.curPos.y;
			var r = this.radius;
			var c = ctx;
			c.fillStyle = this.colour;
			c.beginPath();
			c.arc(x, y, r, 0, Math.PI*2, true);
			c.fill();
//		var gradient = ctx.createRadialGradient((x+(r/4)), (y-(r/4)), (r*0.1), x, y, r);
//    gradient.addColorStop(0.00, "rgba(0,0,0, 0)");
//    gradient.addColorStop(0.9, "rgba(0, 0, 0,0.35)");
//    gradient.addColorStop(0.95, "rgba(0, 0, 0,0.45)");
//    gradient.addColorStop(1, "rgba(0, 0, 0,0.55)");
//    c.fillStyle = gradient;
//      c.fill();


//     c.strokeStyle="rgba(0,0,0,0.7)";
//     c.stroke();

		};
	};
	
	init();
}); 
