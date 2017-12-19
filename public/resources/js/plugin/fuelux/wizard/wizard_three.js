/*
 * Fuel UX Wizard
 * https://github.com/ExactTarget/fuelux
 *
 * Copyright (c) 2012 ExactTarget
 * Licensed under the MIT license.
 */



	//var $   = require('jquery');
	var old = $.fn.wizard;

	// WIZARD CONSTRUCTOR AND PROTOTYPE

	var Wizard = function (element, options) {
		var kids;

		this.$element = $(element);
		this.options = $.extend({}, $.fn.wizard.defaults, options);
		this.options.disablePreviousStep = ( this.$element.data().restrict === "previous" ) ? true : false;
		this.currentStep = this.options.selectedItem.step;
		this.numSteps = this.$element.find('.steps li').length;
		this.$prevBtn = this.$element.find('button.btn-prev');
		this.$nextBtn = this.$element.find('button.btn-next');

		kids = this.$nextBtn.children().detach();
		this.nextText = $.trim(this.$nextBtn.text());
		this.$nextBtn.append(kids);

		// handle events
		this.$prevBtn.on('click', $.proxy(this.previous, this));
		this.$nextBtn.on('click', $.proxy(this.next, this));
		this.$element.on('click', 'li.complete', $.proxy(this.stepclicked, this));
		
		if(this.currentStep > 1) {
			this.selectedItem(this.options.selectedItem);
		}

		if( this.options.disablePreviousStep ) {
			this.$prevBtn.attr( 'disabled', true );
			this.$element.find( '.steps' ).addClass( 'previous-disabled' );
		}
	};

	Wizard.prototype = {

		constructor: Wizard,

		setState: function () {
			var canMovePrev = (this.currentStep > 1);
			var firstStep = (this.currentStep === 1);
			var lastStep = (this.currentStep === this.numSteps);

			// disable buttons based on current step
			if( !this.options.disablePreviousStep ) {
				this.$prevBtn.attr('disabled', (firstStep === true || canMovePrev === false));
			}

			// change button text of last step, if specified
			var data = this.$nextBtn.data();
			if (data && data.last) {
				this.lastText = data.last;
				if (typeof this.lastText !== 'undefined') {
					// replace text
					var text = (lastStep !== true) ? this.nextText : this.lastText;
					var kids = this.$nextBtn.children().detach();
					this.$nextBtn.text(text).append(kids);
				}
			}

			// reset classes for all steps
			var $steps = this.$element.find('.steps li');
			$steps.removeClass('active').removeClass('complete');
			$steps.find('span.badge').removeClass('badge-info').removeClass('badge-success');

			// set class for all previous steps
			var prevSelector = '.steps li:lt(' + (this.currentStep - 1) + ')';
			var $prevSteps = this.$element.find(prevSelector);
			$prevSteps.addClass('complete');
			$prevSteps.find('span.badge').addClass('badge-success');

			// set class for current step
			var currentSelector = '.steps li:eq(' + (this.currentStep - 1) + ')';
			var $currentStep = this.$element.find(currentSelector);
			$currentStep.addClass('active');
			$currentStep.find('span.badge').addClass('badge-info');

			// set display of target element
			var target = $currentStep.data().target;
			this.$element.next('.step-content').find('.step-pane').removeClass('active');
			$(target).addClass('active');

			// reset the wizard position to the left
			this.$element.find('.steps').first().attr('style','margin-left: 0');

			// check if the steps are wider than the container div
			var totalWidth = 0;
			this.$element.find('.steps > li').each(function () {
				totalWidth += $(this).outerWidth();
			});
			var containerWidth = 0;
			if (this.$element.find('.actions').length) {
				containerWidth = this.$element.width() - this.$element.find('.actions').first().outerWidth();
			} else {
				containerWidth = this.$element.width();
			}
			if (totalWidth > containerWidth) {
			
				// set the position so that the last step is on the right
				var newMargin = totalWidth - containerWidth;
				this.$element.find('.steps').first().attr('style','margin-left: -' + newMargin + 'px');
				
				// set the position so that the active step is in a good
				// position if it has been moved out of view
				if (this.$element.find('li.active').first().position().left < 200) {
					newMargin += this.$element.find('li.active').first().position().left - 200;
					if (newMargin < 1) {
						this.$element.find('.steps').first().attr('style','margin-left: 0');
					} else {
						this.$element.find('.steps').first().attr('style','margin-left: -' + newMargin + 'px');
					}
				}
			}

			this.$element.trigger('changed');
		},

		stepclicked: function (e) { 
			var li          = $(e.currentTarget);
			var index       = this.$element.find('.steps li').index(li);
			var canMovePrev = true;

			if( this.options.disablePreviousStep ) {
				if( index < this.currentStep ) {
					canMovePrev = false;
				}
			}

			if( canMovePrev ) {
				var evt = $.Event('stepclick');
				this.$element.trigger(evt, {step: index + 1});
				if (evt.isDefaultPrevented()) return;

				this.currentStep = (index + 1);
				this.setState();
			}
		},

		previous: function () {
			return false;
			var canMovePrev = (this.currentStep > 1);
			if( this.options.disablePreviousStep ) {
				canMovePrev = false;
			}
			if (canMovePrev) {
				var e = $.Event('change');
				this.$element.trigger(e, {step: this.currentStep, direction: 'previous'});
				if (e.isDefaultPrevented()) return;

				this.currentStep -= 1;
				this.setState();
			}
		},

		next: function () {
			window.location.href="/admin/sale/list";	
			return false;
			//检查日开场数是否为空
			////////////$(o).parent().parent().parent().find("input[name='day_nums[]']").val();
			$("#fuelux-wizard tbody").find("input[name='open_num[]']").each(function(){
				var open_num = $(this).val();
				if(open_num==""){
					alert("日开场数不能为空！");
					$(this).focus();
					return false;
				}
			});
			//日客流数
			$("#fuelux-wizard tbody").find("input[name='day_flow[]']").each(function(){
				var day_flow = $(this).val();
				if(day_flow==""){
					alert("日客流不能为空！");
					$(this).focus();
					return false;
				}
			});
			
			var form = $('#fuelux-wizard');
			var ajax = {
					url: "/admin/sale/save_two", data: form.serialize(), type: 'POST', dataType: 'json', cache: false,
					success: function(data, statusText) {
						
						if (data.status == 1) {
							
							window.location.href="/admin/sale/add_three/id/"+data.id;		
						    	
						}
						else if(data.status==2) {
						     alert(data.message);
						}
						
					},
					error: function(httpRequest, statusText, errorThrown) {
						alert(errorThrown);
					}
				};
				$.ajax(ajax);
			return false;
			var validator = $("#fuelux-wizard").validate({
			    rules: {
			      p_name: {
			        required: true
			      },
			      p_date:"required",
			      market_number:{
			    	  required: true,
			    	  digits:true
			      },
			      flow_factor:{
			    	  required: true,
			    	  number:true 
			      },
			      tickets:"required"
			    },
			    
			    messages: {
			      p_name: "请选择项目名称！",
			      p_date:"请选择分期!",
			      market_number:{
			    	  required: "单场标配人数不能为空！",
			    	  digits:"必须输入整数!"
			      },
			      flow_factor:{
			    	  required:"客流系数不能为空！",
			    	  number:"必须输入合法的数字！"
			      },
			      tickets:"票种不能为空！ "
			    },
			    errorPlacement: function(error, element) {
					 if (error.html() != "") {

			              if( element.attr("name") == "p_name" || element.attr("name") == "p_date" || element.attr("name") == "tickets" ){
			            	  $('#' + element.attr("id")).parent().addClass("state-error");
				              $('#' + element.attr("id")).siblings("span").addClass("note").css({"color":'red'}).html( error.html() );
			              }else{    
			              	$('#' + element.attr("id")).parent().addClass("state-error");
			              	$('#' + element.attr("id")).siblings("i").addClass("note").css({"color":'red'}).html( error.html() );
			              }
		                
		            } else {

			               if( element.attr("name") == "p_name" || element.attr("name") == "p_date" || element.attr("name") == "tickets" )
			               {
			            	   $('#' + element.attr("id")).parent().removeClass("state-error");
			                   $('#' + element.attr("id")).siblings("span").removeClass("note").css({"color":'red'}).html("");
			               }else{
			            	   $('#' + element.attr("id")).parent().removeClass("state-error");
			                   $('#' + element.attr("id")).siblings("i").removeClass("note").css({"color":'red'}).html("");
			               }
			               
							
		                
		            
		            }
		         },
		         success: function(label) {  
					    
		         }
			  });
			if(validator.form()){
				 var form = $('#fuelux-wizard');
				 var ajax = {
						url: "/admin/sale/save_one", data: form.serialize(), type: 'POST', dataType: 'json', cache: false,
						success: function(data, statusText) {
							
							if (data.status == 1) {
								
								window.location.href="/admin/sale/add_two/id/"+data.id;		
							    	
							}
							else if(data.status==2) {
							     alert(data.message);
							}
							
						},
						error: function(httpRequest, statusText, errorThrown) {
							alert(errorThrown);
						}
					};
					$.ajax(ajax);
				return false;
			}
			return false;
			var canMoveNext = (this.currentStep + 1 <= this.numSteps);
			var lastStep = (this.currentStep === this.numSteps);

			if (canMoveNext) {
				var e = $.Event('change');
				this.$element.trigger(e, {step: this.currentStep, direction: 'next'});

				if (e.isDefaultPrevented()) return;

				this.currentStep += 1;
				this.setState();
			}
			else if (lastStep) {
				this.$element.trigger('finished');
			}
		},

		selectedItem: function (selectedItem) {
			var retVal, step;

			if(selectedItem) {

				step = selectedItem.step || -1;

				if(step >= 1 && step <= this.numSteps) {
					this.currentStep = step;
					this.setState();
				}

				retVal = this;
			}
			else {
				retVal = { step: this.currentStep };
			}

			return retVal;
		}
	};


	// WIZARD PLUGIN DEFINITION

	$.fn.wizard = function (option) {
		var args = Array.prototype.slice.call( arguments, 1 );
		var methodReturn;

		var $set = this.each(function () {
			var $this   = $( this );
			var data    = $this.data( 'wizard' );
			var options = typeof option === 'object' && option;

			if( !data ) $this.data('wizard', (data = new Wizard( this, options ) ) );
			if( typeof option === 'string' ) methodReturn = data[ option ].apply( data, args );
		});

		return ( methodReturn === undefined ) ? $set : methodReturn;
	};

	$.fn.wizard.defaults = {
        selectedItem: {step:1}
	};

	$.fn.wizard.Constructor = Wizard;

	$.fn.wizard.noConflict = function () {
		$.fn.wizard = old;
		return this;
	};


	// WIZARD DATA-API

	$(function () {
		$('body').on('mouseover.wizard.data-api', '.wizard', function () {
			var $this = $(this);
			if ($this.data('wizard')) return;
			$this.wizard($this.data());
		});
	});
