document.addEventListener('DOMContentLoaded', function() {

	const cookieName = squatchConsentData.cookieName || 'squatch_consent';
	const banner = document.getElementById('squatch-consent-banner');
	const acceptButton = document.getElementById('squatch-consent-accept');
	const rejectButton = document.getElementById('squatch-consent-reject');

	function getCookie(name) {
		const value = '; ' + document.cookie;
		const parts = value.split('; ' + name + '=');
		if(parts.length === 2) {
			return parts.pop().split(';').shift();
		}
		return null;
	}

	function setCookie(name, value, days) {
		const expires = new Date();
		expires.setTime(
			expires.getTime() + (days * 24 * 60 * 60 * 1000)
		);
		document.cookie =
			name + '=' + value +
			'; expires=' + expires.toUTCString() +
			'; path=/' +
			'; SameSite=Lax';
	}

	function showBanner() {
		if(banner) {
			banner.style.display = 'block';
		}
	}

	function hideBanner() {
		if(banner) {
			banner.style.display = 'none';
		}
	}

	function activateConsentScripts() {
		const scripts = document.querySelectorAll(
			'script[data-squatch-consent]'
		);
		scripts.forEach(function(script) {
			const newScript = document.createElement('script');
			Array.from(script.attributes).forEach(function(attr) {
				if(
					attr.name === 'type' ||
					attr.name === 'data-squatch-consent'
				) {
					return;
				}

				newScript.setAttribute(
					attr.name,
					attr.value
				);
			});

			if(script.src) {
				newScript.src = script.src;
			}

			newScript.text = script.textContent;
			document.head.appendChild(newScript);
		});
	}

	const consent = getCookie(cookieName);
	if(consent === 'accepted') {
		hideBanner();
		activateConsentScripts();
		return;
	}
	
	showBanner();
	if(acceptButton) {
		acceptButton.addEventListener('click', function() {
			setCookie(
				cookieName,
				'accepted',
				365
			);
			hideBanner();
			activateConsentScripts();
		});
	}

	if(rejectButton) {
		rejectButton.addEventListener('click', function() {
			hideBanner();
		});
	}
});