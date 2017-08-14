
$('.js-selectize-reguler').selectize({
 sortField: 'text'
});

$('.js-selectize-multi').selectize({
  sortField: 'text',
  delimiter: ',',
  maxItems: null,
});

$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    startDate: '-3d',
    autoclose: true,
});

$('.clockpicker').clockpicker({
    placement: 'bottom',
    align: 'left',
    autoclose: true,
    'default': '00:00'
});
