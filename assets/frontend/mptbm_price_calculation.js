function mptbm_price_format(price) {
	let price_text = '';
	if (mptbm_currency_position === 'right') {
		price_text = price + mptbm_currency_symbol;
	} else if (mptbm_currency_position === 'right_space') {
		price_text = price + '&nbsp;' + mptbm_currency_symbol;
	} else if (mptbm_currency_position === 'left') {
		price_text = mptbm_currency_symbol + price;
	} else {
		price_text = mptbm_currency_symbol + '&nbsp;' + price;
	}
	return price_text;
}
(function ($) {
	$(document).on('change', '.mptbm_booking_item [data-extra-service-price]', function () {
		$(this).closest('label').toggleClass('active_select');
		let parent = $(this).closest('.mptbm_booking_item');
		let price=parseFloat(parent.find('[data-main-price]').attr('data-main-price'));
		parent.find('[data-extra-service-price]').each(function () {
			if ($(this).is(':checked')) {
				let value=$(this).val();
				$(this).siblings('[name="mptbm_extra_service[]"]').val(value);
				let ex_price = parseFloat($(this).data('extra-service-price'));
				price=price+ex_price;
			}else{
				$(this).siblings('[name="mptbm_extra_service[]"]').val('');
			}
		});
		parent.find('[data-main-price]').html(mptbm_price_format(price));

	});
	$(document).on("click", ".mptbm_book_now[type='button']", function () {
		let parent = $(this).closest('.mptbm_booking_item');
		let start_place = parent.find('[name="mptbm_start_place"]');
		let end_place = parent.find('[name="mptbm_end_place"]');
		if (start_place.val().trim() !== "" && end_place.val().trim() !== "") {
			$.when(mptbm_set_cookie_distance_duration(start_place.value, end_place.value)).done(function () {
				parent.find('.mptbm_add_to_cart').trigger('click');
			});
		}
	});
}(jQuery));