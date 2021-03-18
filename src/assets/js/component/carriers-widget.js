/* global jQuery, notification */
const $ = jQuery;

class CarriersWidget {
	constructor() {
		if (!this.setVars()) {
			return;
		}

		this.setEvents();
	}

	setVars() {
		this.atts = {
			carrier: "data-nt-carrier", // single carrier wrapper
			carrierActiveInput: "data-nt-carrier-input-active", // input inside carrier with activated value
			carrierEnableInput: "data-nt-carrier-input-enable", // input inside carrier with enabled value
			carrierSwitch: "data-nt-carrier-input-switch", // input inside carrier with switch value
			carrierRemoveButton: "data-nt-carrier-remove", // remove button inside carrier
			widget: "data-nt-widget", // widget with buttons for adding carriers, "add" and "abort"
			buttons: "data-nt-buttons", // wrapper with buttons
			button: "data-nt-button", // button to adding carrier
			buttonLink: "data-nt-button-link", // link inside button
			add: "data-nt-widget-add", // button to showing buttons
			abort: "data-nt-widget-abort", // button to hiding buttons
			hidden: "data-nt-hidden" // added to hidden elements
		};

		this.wrapper = document.querySelector(`[${this.atts.widget}]`);
		this.carriers = document.querySelectorAll(`[${this.atts.carrier}]`);
		if (!this.wrapper || !this.carriers.length) {
			return;
		}

		this.carrierHndles = document.querySelectorAll(
			`[${this.atts.carrier}] .hndle`
		);
		this.carrierActiveInputs = document.querySelectorAll(
			`[${this.atts.carrierActiveInput}]`
		);
		this.carrierEnableInputs = document.querySelectorAll(
			`[${this.atts.carrierEnableInput}]`
		);
		this.carrierSwitches = document.querySelectorAll(
			`[${this.atts.carrierSwitch}]`
		);
		this.carrierRemoveButtons = document.querySelectorAll(
			`[${this.atts.carrierRemoveButton}]`
		);
		this.buttonsWrapper = this.wrapper.querySelector(
			`[${this.atts.buttons}]`
		);
		this.buttons = this.buttonsWrapper.querySelectorAll(
			`[${this.atts.button}]`
		);
		this.links = this.buttonsWrapper.querySelectorAll(
			`[${this.atts.buttonLink}]`
		);
		this.buttonAdd = this.wrapper.querySelector(`[${this.atts.add}]`);
		this.buttonAbort = this.wrapper.querySelector(`[${this.atts.abort}]`);

		this.settings = {
			classes: {
				carrierDisabledClass: "closed"
			},
			scrollTime: 1000,
			scrollOffset: 50
		};

		return true;
	}

	setEvents() {
		this.buttonAdd.addEventListener("click", e => {
			e.preventDefault();
			this.showButtons();
		});
		this.buttonAbort.addEventListener("click", e => {
			e.preventDefault();
			this.hideButtons();
		});

		const { length } = this.carriers;
		for (let i = 0; i < length; i++) {
			this.buttons[i].addEventListener("click", e => {
				e.preventDefault();
				this.addCarrier(i);
			});
			this.carrierHndles[i].addEventListener("click", e => {
				e.stopImmediatePropagation();
			});
			this.carrierRemoveButtons[i].addEventListener("click", e => {
				e.preventDefault();
				this.removeCarrier(i);
			});
			this.carrierSwitches[i].addEventListener("change", e => {
				this.toggleCarrier(i, e.target.checked);
			});
		}
	}

	showButtons() {
		this.buttonsWrapper.removeAttribute(this.atts.hidden);
		this.buttonAdd.setAttribute(this.atts.hidden, true);
		this.buttonAbort.removeAttribute(this.atts.hidden);
	}

	hideButtons() {
		this.buttonsWrapper.setAttribute(this.atts.hidden, true);
		this.buttonAdd.removeAttribute(this.atts.hidden);
		this.buttonAbort.setAttribute(this.atts.hidden, true);
	}

	addCarrier(index) {
		this.carriers[index].removeAttribute(this.atts.hidden);
		this.carrierActiveInputs[index].value = 1;
		this.buttons[index].setAttribute(this.atts.hidden, true);

		this.toggleCarrier(index, true);
		this.hideButtons();
		this.toggleWrapperVisibility();

		setTimeout(() => {
			this.scrollToCarrier(index);
		}, 0);
	}

	removeCarrier(index) {
		this.carriers[index].setAttribute(this.atts.hidden, true);
		this.carrierActiveInputs[index].value = "";
		this.buttons[index].removeAttribute(this.atts.hidden);

		this.toggleCarrier(index, false);
		this.toggleWrapperVisibility();
	}

	toggleCarrier(index, status) {
		if (status === true) {
			this.carriers[index].classList.remove(
				this.settings.classes.carrierDisabledClass
			);
			this.carrierEnableInputs[index].value = 1;
		} else {
			this.carriers[index].classList.add(
				this.settings.classes.carrierDisabledClass
			);
			this.carrierEnableInputs[index].value = "";
		}
		this.carrierSwitches[index].checked = status;

		notification.hooks.doAction(
			"notification.carrier.toggled",
			$(this.carriers[index])
		);
	}

	toggleWrapperVisibility() {
		if (this.isAnyCarrierExists() === true) {
			this.wrapper.removeAttribute(this.atts.hidden);
		} else {
			this.wrapper.setAttribute(this.atts.hidden, true);
		}
	}

	isAnyCarrierExists() {
		const { length } = this.carriers;
		for (let i = 0; i < length; i++) {
			if (this.carriers[i].getAttribute(this.atts.hidden) !== null) {
				return true;
			}
		}
		return false;
	}

	scrollToCarrier(index) {
		$("html, body").animate(
			{
				scrollTop: this.getDomElementOffset(this.carriers[index])
			},
			this.settings.scrollTime
		);
	}

	getDomElementOffset(element) {
		const scrollTop =
			document.body.scrollTop || document.documentElement.scrollTop;
		const position = element.getBoundingClientRect();
		return position.top + scrollTop - this.settings.scrollOffset;
	}
}

$(document).ready(function() {
	new CarriersWidget();
});
