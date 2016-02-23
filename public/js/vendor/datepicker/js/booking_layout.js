(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = $('ul.navigationTabs a')
							.bind('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (currentTab.size() == 0) {
			currentTab = $('ul.navigationTabs a:first');
		}
		showTab.apply(currentTab.get(0));
		$('#date').DatePicker({
			flat: true,
			//date: '2008-07-31',
			//current: '2008-07-31',
			calendars: 1,
			starts: 1,
			view: 'years'
		});
		var now = new Date();
		
		$('#date2').DatePicker({
			flat: true,
			date: running_dates, //['2014-03-13', '2014-02-02'],			
			//current: '2014-07-31',
			format: 'Y-m-d',
			calendars: 3,
			mode: 'multiple',
			onRender: function(date) {
				return {
					//disabled: (date.valueOf() < now.valueOf()),
					//className: date.valueOf() == now2.valueOf() ? 'datepickerSpecial' : false
					
				}
			},
			onChange: function(formated, dates) {				
				addInputs('#date2', 'runningdatehours', '#runningdates_box');
			},
			
			starts: 0
		});
		//addInputs('#date2', 'runningdatehours', '#runningdates_box');


		
		
		$('#clearSelection').bind('click', function(){
			$('#date2').DatePickerClear();
			$('#runningdates_box').html('');
			return false;
		});
		
		
		$('#overtime_date').DatePicker({
			flat: true,
			date: overtime_dates, //['2014-03-13', '2014-02-02'],			
			//current: '2014-07-31',
			format: 'Y-m-d',
			calendars: 1,
			mode: 'multiple',
			onRender: function(date) {
				return {					
				}
			},
			onChange: function(formated, dates) {
				addInputs('#overtime_date', 'overtimedatehours', '#overtimedates_box');
			},
			starts: 0
		});		
		
		$('#clearSelection2').bind('click', function(){
			$('#overtime_date').DatePickerClear();
			$('#overtimedates_box').html('');
			return false;
		});
		
		
		
		$('#night_date').DatePicker({
			flat: true,
			date: night_dates, //['2014-03-13', '2014-02-02'],			
			//current: '2014-07-31',
			format: 'Y-m-d',
			calendars: 1,
			mode: 'multiple',
			onRender: function(date) {
				return {					
				}
			},
			onChange: function(formated, dates) {
				addInputs('#night_date', 'nightdatehours', '#nightdates_box');
			},
			starts: 0
		});		
		
		$('#clearSelection3').bind('click', function(){
			$('#night_date').DatePickerClear();
			$('#nightdates_box').html('');
			return false;
		});
		
		$('#weekend_date').DatePicker({
			flat: true,
			date: weekend_dates, //['2014-03-13', '2014-02-02'],			
			//current: '2014-07-31',
			format: 'Y-m-d',
			calendars: 1,
			mode: 'multiple',
			onRender: function(date) {
				return {					
				}
			},
			onChange: function(formated, dates) {
				addInputs('#weekend_date', 'weekenddatehours', '#weekenddates_box');
			},
			starts: 0
		});		
		
		$('#clearSelection4').bind('click', function(){
			$('#weekend_date').DatePickerClear();
			$('#weekenddates_box').html('');
			return false;
		});
		
		
		
		$('#holiday_date').DatePicker({
			flat: true,
			date: holiday_dates, //['2014-03-13', '2014-02-02'],			
			//current: '2014-07-31',
			format: 'Y-m-d',
			calendars: 1,
			mode: 'multiple',
			onRender: function(date) {
				return {					
				}
			},
			onChange: function(formated, dates) {
				addInputs('#holiday_date', 'holidaydatehours', '#holidaydates_box');
			},
			starts: 0
		});		
		
		$('#clearSelection5').bind('click', function(){
			$('#holiday_date').DatePickerClear();
			$('#holidaydates_box').html('');
			return false;
		});
		
		//on submit bind dates to hidden fields	
		$('#submit').click(function() {	
			//alert($('#date2').DatePickerGetDate(true));
			//alert($('#holiday_date').DatePickerGetDate(true));
			$('#running_dates').val($('#date2').DatePickerGetDate(true));
			$('#overtime_dates').val($('#overtime_date').DatePickerGetDate(true));
			$('#night_dates').val($('#night_date').DatePickerGetDate(true));
			$('#weekend_dates').val($('#weekend_date').DatePickerGetDate(true));
			$('#holiday_dates').val($('#holiday_date').DatePickerGetDate(true));
			
		});
		
		
		function addInputs(id, input, box)
		{
			var str = $(id).DatePickerGetDate(true).toString();
				var arr = str.split(",");
				arr.sort();
				var html="";
				
				for(i=0; i<arr.length; i++)				
				{					
					if(arr[i]!='NaN-NaN-NaN')
					html += '<span id="datetxt">'+arr[i]+'</span><input type="text" class="datehours" name="'+input+''+arr[i]+'" /><br/>'
					
				}
				$(box).html(html);
				//return true;
		}
		
		
	};
	
	//var running_dates = $('#date2').DatePickerGetDate(true);
	//alert($('#holiday_date').DatePickerGetDate(false));
	//alert($('#date2').DatePickerGetDate(true));		
	
	var showTab = function(e) {
		var tabIndex = $('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		$(this)
			.addClass('active')
			.blur();
		$('div.tab')
			.hide()
				.eq(tabIndex)
				.show();
	};
	
	EYE.register(initLayout, 'init');
})(jQuery)