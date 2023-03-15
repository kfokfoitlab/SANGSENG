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
})