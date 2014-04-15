jQuery(document).ready(function(){
	jQuery('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
	if(jQuery('[name="gcevents[all_day]"]').is(':checked')) jQuery('.time').hide();	
	});

/**
 * Hide time if all day checked
 * @param  object obj
 */
function allDayChange(obj)
{
	if(jQuery(obj).is(':checked')) jQuery('.time').hide();
	else jQuery('.time').show();	
}