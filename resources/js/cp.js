/**
 * Creates or an extends a OnePass singleton object
 * @return void
 */
var OnePass = $.extend(true, OnePass || {}, {
	/**
	 * Intiate plugin settings functionality on settings page load
	 * @return void
	 */
	initSettings: (function () {
		var group = $('.onepass-settings-section-fields'),
			selects = group.find('select');


		/**
		 * Event handler toggles field groups on section dropdown change
		 * @return void
		 */
		$('#settings-section').change(function () {
			console.log('settings');


			/**
			 * Clear and rebuild options of each select menu in the mappings area
			 * @return void
			 */
			function resetOptions () {
				var el = $(this), 
					setting = el.attr('id').replace('settings-', '');


				el.children('option:not(:first)').remove();


				if (selectedSectionData === null) {
					group.hide();
					return;
				}


				group.show();


				for (var i = 0, len = selectedSectionData.fields.length; i < len; i = i + 1) {
					el.append($('<option></option>')
						.attr('value', selectedSectionData.fields[i].value)
						.text(selectedSectionData.fields[i].label));
				}


				el.val(OnePassSettings.values[setting]);
			}


			var selected = this.options[this.selectedIndex].value, 
				/**
				 * Grep the section that was preselected on page load
				 * @return [object, null] Either an object containing details about the selected section and it's fields, or null if no match found
				 */
				selectedSectionData = (function () {
					for (var i = 0, len = OnePassSettings.sections.length; i < len ; i = i + 1) {
				    	if (OnePassSettings.sections[i].id === selected) {
				    		return OnePassSettings.sections[i];
				    	}
					}


					return null;
				})();

			selects.each(resetOptions);
		}).trigger('change');
	})()
});
