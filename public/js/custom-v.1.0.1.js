
$('.js-selectize-reguler').selectize({
 sortField: 'text'
});

$('.js-selectize-multi').selectize({
  sortField: 'text',
  delimiter: ',',
  maxItems: null,
});

var date = new Date();

$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    daysOfWeekDisabled: '0,6',
    startDate: date,
    autoclose: true,
});

var date = new Date();
$('.datepicker-modul').datepicker({
    format: 'yyyy-mm-dd',
    daysOfWeekDisabled: '0,6',
    daysOfWeekHighlighted: '1',
    daysOfWeekDisabled: "0,2,3,4,5,6",
    startDate: date,
    autoclose: true,
});



$('.clockpicker').clockpicker({
    placement: 'bottom',
    align: 'left',
    autoclose: true,
    'default': '07:00'
});
