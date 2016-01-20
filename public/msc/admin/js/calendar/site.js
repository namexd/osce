// call this from the developer console and you can control both instances
var calendars = {};

$(document).ready( function() {

  // assuming you've got the appropriate language files,
  // clndr will respect whatever moment's language is set to.
  // moment.locale('ru');

  // here's some magic to make sure the dates are happening this month.
  //var thisMonth = moment().format('YYYY-MM');

  var eventArray = [
    { startDate: "2016-1-12", endDate: "2016-1-15", title: '已选中的时间段' },
 /*   { date: thisMonth + '-27', title: 'Single Day Event' }*/
  ];

  // the order of the click handlers is predictable.
  // direct click action callbacks come first: click, nextMonth, previousMonth, nextYear, previousYear, or today.
  // then onMonthChange (if the month changed).
  // finally onYearChange (if the year changed).

  calendars.clndr1 = $('.cal1').clndr({
    events: eventArray,
    // constraints: {
    //   startDate: '2013-11-01',
    //   endDate: '2013-11-15'
    // },
    click: function(target) {

    },
    nextMonth: function() {

    },
    previousMonth: function() {

    },
    multiDayEvents: {
      startDate: 'startDate',
      endDate: 'endDate',
      /*singleDay: 'date'*/
    },
    showAdjacentMonths: true,
    adjacentDaysChangeMonth: false
  });

  // bind both clndrs to the left and right arrow keys
  $(document).keydown( function(e) {
    if(e.keyCode == 37) {
      // left arrow
      calendars.clndr1.back();
    }
    if(e.keyCode == 39) {
      // right arrow
      calendars.clndr1.forward();
    }
  });




});