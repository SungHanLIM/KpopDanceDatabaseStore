jQuery(document).ready(function ($) {
	
		"use strict";
		
	var $window = jQuery(window),
		body = jQuery('body'),
		//windowheight = page.getViewportHeight(),
		sitewidth = $('.site').width(),
		maxwidth = $('.site-main').width(),		
		windowheight = $window.height(),
		pageheight = $( document ).height(),		
		windowwidth = $window.width();		
	
	
	//client carousel
	$('.tx-testimonials').each(function () {
		$(this).owlCarousel({
			autoPlay : 8000,
			stopOnHover : true,
			//navigation:true,
			paginationSpeed : 1000,
			goToFirstSpeed : 2000,
			singleItem : true,
			autoHeight : true,
			//navigationText:	["<i class=\"fa fa-angle-left\"></i>","<i class=\"fa fa-angle-right\"></i>"],
			//theme: "tx-custom-slider",
			addClassActive: true
		});
	});
	
	
	//blog and portfolio carousel
	$('.tx-carousel').each(function () {
	
		var _this = $('.tx-carousel');
		var car_columns = _this.data('columns');
			
		$(this).owlCarousel({
			items : car_columns,
			stopOnHover : true,
			paginationSpeed : 1000,
			navigation : true,
			goToFirstSpeed : 2000,
			singleItem : false,
			autoHeight : true,
			navigationText: ['<span class="genericon genericon-leftarrow"></span>','<span class="genericon genericon-rightarrow"></span>'],
			addClassActive: true,
			theme : "tx-owl-theme"
		});
	});
	
	
	//Products carousel
	$('.tx-prod-carousel').each(function () {
	
		var _this = $(this);
		var car_columns = _this.data('columns');
			
		$(this).children('div').children('ul').owlCarousel({
			items : car_columns,
			stopOnHover : true,
			//navigation:true,
			paginationSpeed : 1000,
			navigation : true,
			goToFirstSpeed : 2000,
			singleItem : false,
			autoHeight : true,
			//navigationText:	["<i class=\"fa fa-angle-left\"></i>","<i class=\"fa fa-angle-right\"></i>"],
			//theme: "tx-custom-slider",
			navigationText: ['<span class="genericon genericon-leftarrow"></span>','<span class="genericon genericon-rightarrow"></span>'],
			addClassActive: true,
			theme : "tx-owl-theme"
		});
	});	
	
	//Related Product
	$('.related.products').each(function () {
	
		var _this = $(this);
		var car_columns = _this.data('columns');
		
		car_columns = 4;
			
		$(this).children('ul').owlCarousel({
			items : car_columns,
			stopOnHover : true,
			//navigation:true,
			paginationSpeed : 1000,
			navigation : true,
			goToFirstSpeed : 2000,
			singleItem : false,
			autoHeight : true,
			//navigationText:	["<i class=\"fa fa-angle-left\"></i>","<i class=\"fa fa-angle-right\"></i>"],
			//theme: "tx-custom-slider",
			navigationText: ['<span class="genericon genericon-leftarrow"></span>','<span class="genericon genericon-rightarrow"></span>'],
			addClassActive: true,
			theme : "tx-owl-theme"
		});
	});
	
	
	$('.tx-slider').each(function () {
		
		var _this = $(this);
		var slider_delay = _this.data('delay');
				
		$(this).owlCarousel({
			autoPlay : slider_delay,
			stopOnHover : true,
			navigation: true,
			paginationSpeed : 1000,
			goToFirstSpeed : 2000,
			singleItem : true,
			autoHeight : true,
			navigationText: ['<span class="genericon genericon-leftarrow"></span>','<span class="genericon genericon-rightarrow"></span>'],
			addClassActive: true,
			theme : "tx-owl-theme",
			pagination : true	
		});
	});			
		
			
	// colorboxpopup
	$('.tx-colorbox').each(function () {
		$(this).colorbox();
	});
	
	// blog area masonry
	//if ( $('.tx-post-row').length > 0 )
	
	$(window).load(function(){		
		$('.tx-masonry').each(function () {
			$(this).masonry({});
		});
	});	
	
	/*
	$('.tx-blog').each(function () {
		
		console.log ('maso');
		
		var _this = $(this);
		var container_3 = document.querySelector('.tx-blog');
		var msnry_3 = new Masonry( container_3, {
		  //itemSelector: '.widget'
		});	
	});
	*/
	
	
	/////////////////////////////////////////////
	// Forcing Wide
	/////////////////////////////////////////////	

	$.fn.widify = function() {
		
		this.each( function() {
			var _this = $(this);
			var fwheight = $(this).children('div').outerHeight();
			var extrawidth = (sitewidth-maxwidth)/2+32;
			
			if(sitewidth > 1200)	
			{
				_this.wrapInner( "<div class='tx-fullwidthinner'></div>" );

				_this.css({"overflow":"visible"});
				_this.children('.tx-fullwidthinner').css({"width":sitewidth+"px","position":"relative","margin-left":"-"+extrawidth+"px","overflow":"hidden"});
				
				//console.log ("yo max width : "+maxwidth+" sitewidth : "+sitewidth+" left: "+extrawidth);				
				
			}
		

			$(window).resize(function() {
				//console.log("resized : "+$('.site').width()+",  Site width : "+sitewidth+", max width : "+maxwidth);
				maxwidth = $('.site-main').width();
				sitewidth = $('.site').width();
				extrawidth = (sitewidth-maxwidth)/2+32;
				
				if(sitewidth > 1200) {
					
					if(!_this.children('div').hasClass('tx-fullwidthinner'))
					{
						_this.wrapInner( "<div style='position: relative; overflow: hidden;' class='tx-fullwidthinner'></div>" );
						console.log("added");
					}					
					
					_this.css({"overflow":"visible"});
					_this.children('.tx-fullwidthinner').css({"width":sitewidth+"px","position":"relative","margin-left":"-"+extrawidth+"px"});				
				} else
				{
					
					if(_this.children('div').hasClass('tx-fullwidthinner'))
					{
						_this.children('.tx-fullwidthinner').children().unwrap();
					}	
					_this.css({"height":"auto","overflow":"hidden"});
					console.log("should be here");		
				}
				
			});				
		
		});	
    };

	// forcing wide
	$('.tx-fullwidthrow').each(function () {
		if( $('body.tx-boxed').length < 1 && $('.has-left-sidebar').length < 1 && $('.has-right-sidebar').length < 1 )
		{
			$(this).widify();
		}		
	});	
	
	


});

