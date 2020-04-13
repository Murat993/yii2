$(function() {
	// Растягивание сайдбара по высоте body
	var body_height = $('body').height();
	var sidebar_height = $('.sidebar').height();

	if(body_height > sidebar_height) {
		$('.sidebar').css('height', body_height - 60);
	}
	
	// Открытие/закрытие меню пользователя в шапке
	$('.profile .media').click(function() {
		$('.profile__dropdown').toggleClass('visible');
	});

	// Открытие/закрытие навигации
	$('.burger').click(function() {
		$(this).toggleClass('active');
		$('.sidebar').slideToggle(250);
	});

	// Чекбокс
	$('.form__checkbox').click(function() {
		$(this).toggleClass('checked');

		var value = $(this).hasClass('checked') ? 1 : 0;
		$(this).find('input[type=hidden]').val(value);
	});

	// Опросы
	$('.report__item, .interview__item').click(function() {
        if(!$(this).parents('.interview__item, .report__item').length) {
            $(this).toggleClass('uncollapsed');
            $('.interview__childs > div:first-child').css('margin-top','0px');
            $('.interview__item:last-child').css('margin-bottom','10px');
            $(this).siblings('.interview__childs, .report__childs').slideToggle();
            $(this).parents().siblings().find('.interview__childs, .report__childs').slideUp();
            $(this).siblings().removeClass('uncollapsed');
        }
	});
	

	// Модалка "Опросы", вкладки
	$('.interview__tab').click(function() {
		$('.interview__tab').removeClass('active');
		$(this).addClass('active');

		var content = $(this).data('content');

		$('.interview__content').removeClass('visible');
		$('#' + content).addClass('visible');

		if(content == 'comments') {
			$(this).parents('.modal').find('.btn-info').removeClass('d-none');
		} else {
			$(this).parents('.modal').find('.btn-info').addClass('d-none');
		}
	});

	$(document).mouseup(function (e){
		var dropdown = $('.profile__dropdown');
		if (!dropdown.is(e.target)
		    && dropdown.has(e.target).length === 0) {
			dropdown.removeClass('visible');
		}
	});
});