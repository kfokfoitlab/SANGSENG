document.addEventListener("DOMContentLoaded", function() {
	// index main banner slide
	if(!!document.querySelector('.main-banner-swiper')) {
		const mainSwiper = new Swiper(".main-banner-swiper", {
			pagination: {
				el: ".swiper-pagination",
				type: "fraction",
				formatFractionCurrent: function(number) {
					return ('0' + number).slice(-2);
				},
				formatFractionTotal: function(number) {
					return ('0' + number).slice(-2);
				},
				renderFraction: function(currentClass, totalClass) {
					return '<span class="' + currentClass + '"></span>'
							+ '<span class="' + totalClass + '"></span>'
				},
			},
			scrollbar: {
				el: '.swiper-scrollbar',
				draggable: false,
				dragSize: 20,
			},
			autoplay: {
				delay: 5000,
				disableOnInteraction: false,
			},
			loop: true
		});
		
		const btnPlayToggle = document.querySelector('.btn-play-toggle');
		let playToggleStatus = true;
		btnPlayToggle.addEventListener('click', function() {
			if(playToggleStatus) {
				mainSwiper.autoplay.stop();
				btnPlayToggle.style.backgroundImage = 'url("/assets/m_images/play_start.png")';
				playToggleStatus = !playToggleStatus;
			} else {
				mainSwiper.autoplay.start();
				btnPlayToggle.style.backgroundImage = 'url("/assets/m_images/play_stop.png")';
				playToggleStatus = !playToggleStatus;
			}
		})
	}
	// index main collapse
	if(!!document.querySelector('.main-collapse')) {
		const collapseCtt = document.querySelector('.main-collapse-content');
		const collapseBtn = document.querySelector('.main-collapse-btn');
		let collapseStatus = false;
		collapseBtn.addEventListener('click', function() {
			if(!collapseStatus) {
				collapseCtt.style.display = 'block';
				collapseBtn.style.backgroundImage = 'url(/assets/m_images/main_collapse_up.png)'
				collapseStatus = !collapseStatus;
			} else {
				collapseCtt.style.display = 'none';
				collapseBtn.style.backgroundImage = 'url(/assets/m_images/main_collapse_down.png)'
				collapseStatus = !collapseStatus;
			}
		})
	}
	// index main recommended Item slide
	if(!!document.querySelector('.recommended-item-swiper')) {
		const recommenedItemswiper = new Swiper(".recommended-item-swiper", {
			slidesPerView: "auto",
			spaceBetween: 16,
			scrollbar: {
				el: ".swiper-scrollbar",
				hide: false,
			},
		});
	}
	// index main new items list
	if(!!document.querySelector('.new-items-bottom')) {
		const newItemMoreBtn = document.querySelector('.new-items-more-btn');
		const newItemMoreArrow = newItemMoreBtn.querySelector('span');
		const moreNewItems = document.querySelector('.new-items-bottom')
		let moreNewItemsStatus = false;
		newItemMoreBtn.addEventListener('click', function() {
			if(!moreNewItemsStatus) {
				moreNewItems.style.display = 'flex';
				newItemMoreArrow.style.transform = 'translateY(2px) rotate(225deg)';
				moreNewItemsStatus = !moreNewItemsStatus;
			} else {
				moreNewItems.style.display = 'none';
				newItemMoreArrow.style.transform = 'translateY(-2px) rotate(45deg)';
				moreNewItemsStatus = !moreNewItemsStatus;
			}
		})
	}
	// index media
	if(!!document.querySelector('.media-list-swiper')) {
		$(function(){
			let windowWidth = $(window).width() - 40;
			let iHeight = (windowWidth / 16) * 9;
			
			$("#media01").iziModal({
				width: windowWidth,
				iframeHeight: iHeight,
				transitionIn: "fadeIn",
				transitionOut: "fadeOut",
				iframe: true,
				closeButton: true,
			});
			$("#media02").iziModal({
				width: windowWidth,
				iframeHeight: iHeight,
				transitionIn: "fadeIn",
				transitionOut: "fadeOut",
				iframe: true,
				closeButton: true,
			});
			$("#media03").iziModal({
				width: windowWidth,
				iframeHeight: iHeight,
				transitionIn: "fadeIn",
				transitionOut: "fadeOut",
				iframe: true,
				closeButton: true,
			});
			
			// $("#westham").iziModal({
			// 	width: windowWidth,
			// 	height: iHeight,
			// 	radius: 5,
			// 	closeButton: true,
			// });
		});
		
		const mediaListSwiper = new Swiper(".media-list-swiper", {
			slidesPerView: "auto",
			spaceBetween: 16,
		});
	}
	// signIn
	if(!!document.querySelector('.auth_signIn')) {
		const signInTypeBtn = document.querySelectorAll('.select-company-type button');
		signInTypeBtn.forEach(btn => btn.addEventListener('click', function(){
			signInTypeBtn.forEach(target => target.classList.remove('on'));
			this.classList.add('on');
		}))
		
		const toggleBtn = document.querySelector('.toggle_pwd');
		const pwdInput = document.querySelector('#signInPwd');
		let pwdToggleStatus = 'hide';
		toggleBtn.addEventListener('click', function() {
			if(pwdToggleStatus === 'hide') {
				pwdInput.type = 'text';
				toggleBtn.querySelector('img').src = '/assets/m_images/icon_hide_pwd.png';
				pwdToggleStatus = 'show'
			} else {
				pwdInput.type = 'password';
				toggleBtn.querySelector('img').src = '/assets/m_images/icon_show_pwd.png';
				pwdToggleStatus = 'hide'
			}
		})
	}
	// signUp: select company type
	if(!!document.querySelector('.auth_signUp')) {
		const joinBoxes = document.querySelectorAll('.join-type-box');
		const joinBtn = document.querySelector('.join-btn');
		
		joinBoxes.forEach(box => box.addEventListener('click', function() {
			joinBoxes.forEach(target => {
				target.classList.remove('on');
				target.classList.add('off');
			});
			this.classList.add('on');
			joinBtn.classList.add('on');
		}) )
	}
	// signUpSLA: terms agreement
	if(!!document.querySelector('.auth_signUpSLA')) {
		const inputForm = document.querySelector('#inputChkForm');
		const checkAll = document.querySelector('.input-chk-all');
		const checkBoxes = document.querySelectorAll('.input-chk-form input');
		const agreeSubmitBtn = document.querySelector('.agree-submit-btn');

		const agreements = {
			termsService: false,
			termsPrivacy: false,
			termsAd: false,
		}

		inputForm.addEventListener('submit', e => e.preventDefault());

		checkBoxes.forEach(item => item.addEventListener('input', toggleCheckbox));

		function toggleCheckbox(e) {
			const {checked, id} = e.target;
			agreements[id] = checked;
			checkAllStatus();
			toggleSubmitButton()
		}
		function checkAllStatus() {
			const {termsService, termsPrivacy, termsAd} = agreements;
			if(termsService && termsPrivacy && termsAd) {
				checkAll.checked = true;
			} else {
				checkAll.checked = false;
			}
		}
		function toggleSubmitButton() {
			const {termsService, termsPrivacy} = agreements;
			if(termsService && termsPrivacy) {
				agreeSubmitBtn.disabled = false;
			} else {
				agreeSubmitBtn.disabled = true;
			}
		}

		checkAll.addEventListener('click', e => {
			const { checked } = e.target;
			if(checked) {
				checkBoxes.forEach(item => {
					item.checked = true;
					agreements[item.id] = true;
				})
			} else {
				checkBoxes.forEach(item => {
					item.checked = false;
					agreements[item.id] = false;
				})
			}
			toggleSubmitButton();
		})

		let windowWidth = $(window).width() - 40;
		let iHeight = (windowWidth / 16) * 9;
		$(function(){
			$("#modal_tos").iziModal({
				title: '사이트 이용약관 동의',
				headerColor: '#fff',
				background: '#fff',
				width: windowWidth,
				radius: 8,
				closeButton: true,
				focusInput: false,
				transitionIn: 'fadeIn',
				transitionOut: 'fadeOut',
			});
			$("#modal_privacy").iziModal({
				title: '개인정보취급방침 동의',
				headerColor: '#fff',
				background: '#fff',
				width: windowWidth,
				radius: 8,
				closeButton: true,
				focusInput: false,
				transitionIn: 'fadeIn',
				transitionOut: 'fadeOut',
			});
			$("#modal_marketing").iziModal({
				title: '광고성 정보 수신 동의',
				headerColor: '#fff',
				background: '#fff',
				width: windowWidth,
				radius: 8,
				closeButton: true,
				focusInput: false,
				transitionIn: 'fadeIn',
				transitionOut: 'fadeOut',
			});
		});

		$(document).on("click", ".trigger", function (event) {
			event.preventDefault();
			$("#modal").iziModal("open");
		});
	}
})