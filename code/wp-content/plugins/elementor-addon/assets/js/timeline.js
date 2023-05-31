/* Timeline section */

jQuery(document).ready(function ($) {
	if(('.timeline-section')[0]){
		var c = $(".timeline-year").offset().left;
		$('.year-data').first().css('margin-left', c);
		$('.year-data').last().css('margin-right', c);

		
	}
});


if(('.timeline-section')[0]){
	let isProgrammaticScroll = false;
	const timeLineHeader = document.getElementsByClassName("timeline-year")[0];
	const container = document.querySelector(".timeline-list");

	// keep 1st tab selected on pageload
	document.addEventListener("DOMContentLoaded", function (event) {
	document
		.querySelector(".timeline-year li.tab:first-child")
		.classList.add("selected");
	});


	function selectTab(year) {
		// loop through all tabs, remove class selected and add to specific selected tab
		const tabs = document.querySelectorAll(".tab");
		tabs.forEach((tab) => tab.classList.remove("selected"));
		const selectedTab = document.getElementById("tab-" + year);
		selectedTab.classList.add("selected");

		// select particular card corresponding to the selected tab
		const card = document.querySelector('.year-data[data-year="' + year + '"]');

		// calculate left scroll value for selected card
		const leftScrollValue = card.offsetLeft - timeLineHeader.offsetLeft;
		const cardsContainer = document.getElementsByClassName("timeline-list")[0];
		cardsContainer.scroll({
			left: leftScrollValue,
			behavior: "smooth",
		});
		isProgrammaticScroll = true;
		setTimeout(() => {
			isProgrammaticScroll = false;
		}, 2000);
	}

	container.addEventListener("scroll", function (event) {
	const cards = document.querySelectorAll(".year-data");
	const tabs = document.querySelectorAll(".tab");
	if (!isProgrammaticScroll) {
		for (let i = 0; i < cards.length; i++) {
		const card = cards[i];
		const cardRect = card.getBoundingClientRect();
		if (cardRect.left < 6 + timeLineHeader.offsetLeft) {
			tabs.forEach((tab) => tab.classList.remove("selected"));
			tabs[i].classList.add("selected");
		}
		}
	}
	});

	// horizontal drag feature

	let isMouseDown = false;
	let startX;
	let scrollLeft;

	container.addEventListener("mousedown", (e) => {
	isMouseDown = true;
	container.classList.add("dragging");
	startX = e.pageX - container.offsetLeft;
	scrollLeft = container.scrollLeft;
	});

	container.addEventListener("mouseleave", () => {
	isMouseDown = false;
	container.classList.remove("dragging");
	});

	container.addEventListener("mouseup", () => {
	isMouseDown = false;
	container.classList.remove("dragging");
	});

	container.addEventListener("mousemove", (e) => {
	if (!isMouseDown) return;
	e.preventDefault();
	const x = e.pageX - container.offsetLeft;
	const walk = (x - startX) * 2; // Adjust the scroll speed by changing the multiplier
	container.scrollLeft = scrollLeft - walk;
	});
}