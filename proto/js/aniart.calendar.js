if(window.Aniart){
	(function($){
		var PREV_YEAR = 0; //common for all ^^
		var functions = {
			init: function(options){
				//var outerArguments = arguments;
				var settings = $.extend({
					date			: new Date(),
					yearsStart		: 0,
					yearsEnd		: 0,
					onDateChange	: $.noop,
					onYearChange	: $.noop,
					onMonthChange	: $.noop,
					onDayChange		: $.noop,
					onShow			: $.noop,
					onHide			: $.noop,
					onRemove		: $.noop,
					onInit			: $.noop,
				}, options);
				return this.each(function(){
					var Years = {
						jqContainer: $(Aniart.hereDoc(function(){
							/*
							 <div class="years"></div>
							 */
						})),
						current: {
							jqContainer: null,
							value: 0
						},
						takePosition: function(){
							var yearContainer	= this.jqContainer;
							var yearCurrent		= this.current.jqContainer;
							var yearPosition 	= yearCurrent.offset().top - yearContainer.offset().top + yearContainer.scrollTop() - 5*(yearCurrent.height() + 3);
							
							if(PREV_YEAR){
								var yearPrev = this.jqContainer.find('a:contains("'+PREV_YEAR+'")');
								if(yearPrev.length){
									yearPrevPosition = yearPrev.offset().top - yearContainer.offset().top + yearContainer.scrollTop() - 5*(yearPrev.height() + 3);
									yearContainer.scrollTop(yearPrevPosition);
								}
							}
							PREV_YEAR = this.current.value;
							
							yearContainer.animate({
								scrollTop: yearPosition
							}, 250, 'swing');
						},
						set: function(year){
							this.current.value = year;
							this.jqContainer.find('a.act').removeClass('act');
							if(arguments[1]){
								var jqObject = $(arguments[1]);
							}
							else{
								var jqObject = this.jqContainer.find('a:contains("'+this.current.value+'")');
							}
							jqObject.addClass('act');
							this.current.jqContainer = jqObject;
						},
						init: function(year, yearsStart, yearsEnd, onChangeYearCallback){
							var _this = this;
							if(!yearsStart){
								yearsStart = year - 1;
							}
							else if(year < yearsStart){
								yearsStart = year;
							}
							if(!yearsEnd){
								yearsEnd = year + 1;
							}
							else if(year > yearsEnd){
								yearsEnd = year; 
							}
							
							for(var i = yearsStart; i <= yearsEnd; i++){
								var a = $('<a>', {text: i, href: "#"});
								a.on('click.aniartCalendar', function(event){
									event.preventDefault();
									_this.set($(this).html(), this);
									if(typeof onChangeYearCallback == 'function'){
										onChangeYearCallback.apply(_this);
									}
									return false;
								});
								this.jqContainer.append($('<div>', {'class': 'one-year'}).append(a));
							}
							this.set(year);
						}
					};
					
					var Months = {
						jqContainer: $(Aniart.hereDoc(function(){
							/*
			                <div class="cal-month">
			                    <div class="month-name">
			                        Март
			                    </div>
			                    <a href="javascript:void(0)" class="prev-m"></a>
			                    <a href="javascript:void(0)" class="next-m"></a>
			                </div>					 
			                */
						})),
						months	:['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
						monthsT	: ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'],
						current	: {month: 0, name: '', nameT: ''},
						set: function(month){
							this.current.month	= month;
							if(month < 0){
								month = 11;
							}
							else if(month > 11){
								month = 0
							}
							this.current.name	= this.months[month];
							this.current.nameT	= this.monthsT[month];
							this.jqContainer.find('.month-name').text(this.current.name);
						},
						init: function(month, onChangeMonthCallback){
							var _this = this;
							this.jqContainer.on('click.aniartCalendar', '.prev-m', function(){
								_this.set(_this.current.month - 1);
								if(typeof onChangeMonthCallback == 'function'){
									onChangeMonthCallback.apply(_this);
								}
							});
							this.jqContainer.on('click.aniartCalendar', '.next-m', function(){
								_this.set(_this.current.month + 1);
								if(typeof onChangeMonthCallback == 'function'){
									onChangeMonthCallback.apply(_this);
								}
							});
							this.set(month);
						}
					};
					
					var Days = {
						jqContainer: $(Aniart.hereDoc(function(){
							/*
			                 <div class="week">
			                 	<div class="week-name">
			                 	</div>
			                 	<div class="week-num">
			                 	</div>                     	
			                 </div>
			            	 */
						})),
						weekDays: ['Вс', "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
						current: 0,
						set: function(day){
							this.current = day;
							this.jqContainer.find('a.act').removeClass('act');
							if(arguments[1]){
								var jqObject = $(arguments[1]);
								jqObject.addClass('act');
							}
							else{
								this.jqContainer.find('.week-num a[rel="'+day+'"]').addClass('act');
							}
						},
						init: function(date, onChangeDayCallback){
							var weekContainer = this.jqContainer.find('.week-name');
							this.weekDays.forEach(function(weekDay){
								weekContainer.append('<span>'+weekDay+'</span>');
							});
							var _this			= this;
							var year			= date.getFullYear();
							var month			= date.getMonth();
							var day				= date.getDate();
							var daysInMonth 	= new Date(year, month-1, 0).getDate();
							var daysInPrevMonth	= new Date(year, month-2, 0).getDate();
							var firstWeekDay	= new Date(year, month).getDay();
							var lastWeekDay		= new Date(year, month + 1).getDay() - 1;
							var daysInGrid		= 6*7//daysInMonth + firstWeekDay + ( 6 - lastWeekDay);
							
							for(var i = (0 - firstWeekDay); i < (daysInGrid - firstWeekDay); i++){
								var a = $('<a>', {href: '#', text: i+1, rel: i+1});
								if(i < 0 || i >= daysInMonth){
									a.addClass('prev-n');
									if(i <= 0){
										a.text(daysInPrevMonth+(i+1));
									}
									else{
										a.text((i+1)-daysInMonth);
									}
								}
								a.on('click.aniartCalendar', {index: i+1}, function(event){
									event.preventDefault();
									_this.set(event.data.index);
									if(typeof onChangeDayCallback == 'function'){
										onChangeDayCallback.apply(_this, this);
									}
									return false;
								});
								this.jqContainer.find('.week-num').append(a);
							}
							this.set(day);
						}
					};
					
					var $this			= $(this);
					var selectedDate 	= settings.date;
					var jqContainer 	= $(Aniart.hereDoc(function(){
						/*
		                  <div class="cal">
		                  	<input type="hidden" />
		                  	 <div class="cal-in">
		                         <div class="cal-left">
		                         	<div class="head-cal">
		                         	</div>
		                         </div>
		                         <div class="cal-right">
		                         </div>
		                     </div>
		                  </div>
						 */
					}));
					
					Years.init(selectedDate.getFullYear(), settings.yearsStart, settings.yearsEnd, function(){
						var year = this.current.value;
						$this.aniartCalendar('setYear', year);
					});
					Months.init(selectedDate.getMonth(), function(){
						var month = this.current.month;
						$this.aniartCalendar('setMonth', month);
					});
					Days.init(selectedDate, function(){
						var day = this.current;
						$this.aniartCalendar('setDay', day);
					});
					
					jqContainer.find('.head-cal').append(Months.jqContainer, Days.jqContainer);
					jqContainer.find('.cal-right').append(Years.jqContainer);
					jqContainer.find('input').attr({name: $this.attr('name')+'_timestamp', value: Math.round(selectedDate.getTime()/1000)});

					$this
						.attr('autocomplete', 'off')
						.addClass('aniartCalendar')
						.val([Days.current, Months.current.nameT + ', ', + Years.current.value + ' год'].join(' '))
						.after(jqContainer)
						.on('focus.aniartCalendar', function(){
							if(jqContainer.is(':not(.act)')){
								$this.aniartCalendar('show');
							}
							else{
								$this.aniartCalendar('hide');
							}
						})
						.on('keypress.aniartCalendar', function(event){
							event.preventDefault();
							event.stopPropagation();
							return false;
						});
					Years.takePosition();
					
					$this.data('aniartCalendar', {
						jqContainer: 	jqContainer,
						selectedDate:	selectedDate,
						settings:		settings
					});
					
					if(typeof settings.onInit == 'function'){
						settings.onInit.call($this, settings);
					}
				});
			},
			getData: function(){
				return this.data('aniartCalendar');
			},
			getDate: function(){
				var data = this.aniartCalendar('getData');
				if(data){
					return data.selectedDate;
				}
			},
			setDate: function(date){
				return this.each(function(){
					var $this = $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						$this.aniartCalendar('reinit', {date: date});
						if(arguments[1] !== false, typeof data.settings.onDateChange == 'function'){
							data.settings.onDateChange.call($this);
						}
					}
				});
			},
			setYear: function(year){
				return this.each(function(){
					var $this = $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						data.selectedDate.setYear(year);
						$this.aniartCalendar('setDate', data.selectedDate, false);
						if(typeof data.settings.onYearChange == 'function'){
							data.settings.onYearChange.call($this);
						}
					}
				});
			},
			setMonth: function(month){
				return this.each(function(){
					var $this = $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						data.selectedDate.setMonth(month);
						$this.aniartCalendar('setDate', data.selectedDate, false);
						if(typeof data.settings.onMonthChange == 'function'){
							data.settings.onMonthChange.call($this);
						}
					}
				});
			},
			setDay: function(day){
				return this.each(function(){
					var $this = $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						data.selectedDate.setDate(day);
						$this.aniartCalendar('setDate', data.selectedDate, false);
						if(typeof data.settings.onDayChange == 'function'){
							data.settings.onDayChange.call($this);
						}
					}
				});
			},
			show: function(){
				return this.each(function(){
					var $this	= $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						$('.aniartCalendar').aniartCalendar('hide');
						if(!data.jqContainer.is('.act')){
							$this.css('position', 'relative');
							data.jqContainer.addClass('act');
							if(typeof data.settings.onShow == 'function'){
								data.settings.onShow.call($this);
							}
						}	
					}
				});
			},
			hide: function(){
				return this.each(function(){
					var $this	= $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						if(data.jqContainer.is('.act')){
							$this.css('position', 'static');
							data.jqContainer.removeClass('act');
							if(typeof data.settings.onHide == 'function'){
								data.settings.onHide.call($this);
							}
						}
					}
				});
			},
			remove: function(){
				return this.each(function(){
					var $this = $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						data.jqContainer.remove();
						$this.unbind('.aniartCalendar');
						$this.data(null);
						if(typeof data.settings.onRemove == 'function'){
							data.settings.onRemove.call($this);
						}
					}
				});
			},
			reinit: function(options){
				options = options || {};
				return this.each(function(){
					var $this = $(this);
					var data = $this.aniartCalendar('getData');
					if(data){
						var settings = $.extend(data.settings, options);
						$this.aniartCalendar('remove')
							 .aniartCalendar(settings)
							 .aniartCalendar('show');
					}
				});
			}
		};
		
		$.fn.aniartCalendar = function(funcName){
			if(functions[funcName]){
				return functions[funcName].apply(this, Array.prototype.slice.call(arguments, 1));
			}
			else if(typeof funcName === 'object' || !funcName){
				return functions.init.apply(this, arguments);
			}
			else{
				return false;
			}
		};
	})(jQuery);
}