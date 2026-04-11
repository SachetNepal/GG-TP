document.addEventListener('DOMContentLoaded', function () {
  var toggleButton = document.querySelector('[data-menu-toggle]');
  var mainNav = document.querySelector('[data-main-nav]');

  if (toggleButton && mainNav) {
    toggleButton.addEventListener('click', function () {
      var isOpen = mainNav.classList.toggle('open');
      toggleButton.setAttribute('aria-expanded', String(isOpen));
    });
  }

  var contactForm = document.getElementById('contactForm');
  if (!contactForm) return;

  var formSuccess = document.getElementById('formSuccess');
  var fields = {
    firstName: {
      element: document.getElementById('firstName'),
      validate: function (value) {
        return value.trim().length >= 2;
      },
      message: 'Please enter a valid first name.'
    },
    lastName: {
      element: document.getElementById('lastName'),
      validate: function (value) {
        return value.trim().length >= 2;
      },
      message: 'Please enter a valid last name.'
    },
    email: {
      element: document.getElementById('email'),
      validate: function (value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
      },
      message: 'Please enter a valid email address.'
    },
    mobile: {
      element: document.getElementById('mobile'),
      validate: function (value) {
        return /^[0-9+\s()-]{7,}$/.test(value.trim());
      },
      message: 'Please enter a valid mobile number.'
    },
    message: {
      element: document.getElementById('message'),
      validate: function (value) {
        return value.trim().length >= 10;
      },
      message: 'Please enter at least 10 characters.'
    }
  };

  function setError(fieldName, message) {
    var errorEl = document.querySelector('[data-error-for="' + fieldName + '"]');
    if (errorEl) errorEl.textContent = message || '';
    if (fields[fieldName] && fields[fieldName].element) {
      fields[fieldName].element.classList.toggle('invalid', Boolean(message));
    }
  }

  contactForm.addEventListener('submit', function (event) {
    event.preventDefault();
    var isValid = true;
    formSuccess.textContent = '';

    Object.keys(fields).forEach(function (key) {
      var field = fields[key];
      var value = field.element ? field.element.value : '';
      if (!field.validate(value)) {
        setError(key, field.message);
        isValid = false;
      } else {
        setError(key, '');
      }
    });

    if (!isValid) return;

    formSuccess.textContent = 'Thanks! Your message has been sent successfully.';
    contactForm.reset();
  });
});
