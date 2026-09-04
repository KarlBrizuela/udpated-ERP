// Modal Utility Functions - Reusable modal system to replace alerts
// Initialize Bootstrap modals
let alertModal, confirmModal, promptModal;

document.addEventListener('DOMContentLoaded', function() {
	const alertModalEl = document.getElementById('alertModal');
	const confirmModalEl = document.getElementById('confirmModal');
	const promptModalEl = document.getElementById('promptModal');
	
	if (alertModalEl) alertModal = new bootstrap.Modal(alertModalEl);
	if (confirmModalEl) confirmModal = new bootstrap.Modal(confirmModalEl);
	if (promptModalEl) promptModal = new bootstrap.Modal(promptModalEl);
});

// Replace alert() with modal
function showAlert(message, type = 'info') {
	const modal = document.getElementById('alertModal');
	if (!modal) {
		console.warn('Alert modal not found. Using native alert.');
		return alert(message);
	}
	
	const header = document.getElementById('alertModalHeader');
	const messageEl = document.getElementById('alertModalMessage');
	
	if (!messageEl) {
		console.warn('Alert modal elements not found. Using native alert.');
		return alert(message);
	}
	
	// Set message
	messageEl.textContent = message;
	
	// Set header color based on type
	if (header) {
		header.className = 'modal-header';
		if (type === 'success') {
			header.classList.add('bg-success', 'text-white');
		} else if (type === 'error' || type === 'danger') {
			header.classList.add('bg-danger', 'text-white');
		} else if (type === 'warning') {
			header.classList.add('bg-warning', 'text-dark');
		} else {
			header.classList.add('bg-info', 'text-white');
		}
	}
	
	if (alertModal) {
		alertModal.show();
	}
}

// Replace confirm() with modal
function showConfirm(message, callback) {
	const modal = document.getElementById('confirmModal');
	if (!modal) {
		console.warn('Confirm modal not found. Using native confirm.');
		const result = confirm(message);
		if (callback) callback(result);
		return;
	}
	
	const messageEl = document.getElementById('confirmModalMessage');
	const okBtn = document.getElementById('confirmModalOkBtn');
	
	if (!messageEl || !okBtn) {
		console.warn('Confirm modal elements not found. Using native confirm.');
		const result = confirm(message);
		if (callback) callback(result);
		return;
	}
	
	messageEl.textContent = message;
	
	// Remove previous event listeners
	const newOkBtn = okBtn.cloneNode(true);
	okBtn.parentNode.replaceChild(newOkBtn, okBtn);
	
	// Add new event listener
	newOkBtn.addEventListener('click', function() {
		if (confirmModal) confirmModal.hide();
		if (callback) callback(true);
	});
	
	if (confirmModal) {
		confirmModal.show();
	}
}

// Replace prompt() with modal
function showPrompt(message, defaultValue = '', callback) {
	const modal = document.getElementById('promptModal');
	if (!modal) {
		console.warn('Prompt modal not found. Using native prompt.');
		const result = prompt(message, defaultValue);
		if (callback) callback(result);
		return;
	}
	
	const messageEl = document.getElementById('promptModalMessage');
	const inputEl = document.getElementById('promptModalInput');
	const okBtn = document.getElementById('promptModalOkBtn');
	
	if (!messageEl || !inputEl || !okBtn) {
		console.warn('Prompt modal elements not found. Using native prompt.');
		const result = prompt(message, defaultValue);
		if (callback) callback(result);
		return;
	}
	
	messageEl.textContent = message;
	inputEl.value = defaultValue;
	
	// Remove previous event listeners
	const newOkBtn = okBtn.cloneNode(true);
	okBtn.parentNode.replaceChild(newOkBtn, okBtn);
	
	// Add new event listener
	newOkBtn.addEventListener('click', function() {
		const value = inputEl.value;
		if (promptModal) promptModal.hide();
		if (callback) callback(value);
	});
	
	if (promptModal) {
		promptModal.show();
		setTimeout(() => {
			inputEl.focus();
			inputEl.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					newOkBtn.click();
				}
			});
		}, 500);
	}
}

// Override native alert, confirm, prompt (optional - can be disabled if needed)
if (typeof window !== 'undefined') {
	// Only override if modals are available
	if (document.getElementById('alertModal')) {
		window.alert = showAlert;
	}
	if (document.getElementById('confirmModal')) {
		window.confirm = function(message) {
			return new Promise((resolve) => {
				showConfirm(message, (result) => {
					resolve(result);
				});
			});
		};
	}
	if (document.getElementById('promptModal')) {
		window.prompt = function(message, defaultValue) {
			return new Promise((resolve) => {
				showPrompt(message, defaultValue || '', (value) => {
					resolve(value);
				});
			});
		};
	}
}
