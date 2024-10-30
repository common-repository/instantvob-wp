(function () {
  if (!document.querySelector('#instantvob_form')) {
    return;
  }

  const validator = new window.JustValidate('#instantvob_form', {
    validateBeforeSubmitting: true,
  });

  validator
    .addField('#instantvob_form_first_name', [
      {
        rule: 'required',
      },
    ])
    .addField('#instantvob_form_last_name', [
      {
        rule: 'required',
      },
    ])
    .addField('#instantvob_form_dob', [
      {
        rule: 'required',
      },
    ])
    .addField('#instantvob_form_phone', [
      {
        rule: 'required',
      },
    ])
    .addField('#instantvob_form_email', [
      {
        rule: 'required',
      },
    ])
    .addField('#instantvob_form_insurance', [
      {
        rule: 'required',
      },
    ])
    .addField('#instantvob_form_insurance_id', [
      {
        rule: 'required',
      },
    ]);

  jQuery(document).on('submit', '#instantvob_form', function (e) {
    e.preventDefault();

    function convertFormToJSON(form) {
      const array = jQuery(form).serializeArray();
      const json = {};
      jQuery.each(array, function () {
        json[this.name] = this.value || '';
      });
      return json;
    }

    function formatDate(date) {
      let dd = (date.getUTCDate() < 10 ? '0' : '') + (date.getUTCDate());

      let MM = (date.getUTCMonth() < 10 ? '0' : '') + (date.getUTCMonth() + 1);

      return `${MM}/${dd}/${date.getUTCFullYear()}`;
    }

    validator.revalidate().then((isValid) => {
      console.log('isValid', isValid);
      // Empty any messages before proceeding
      jQuery('.instantvob_form_error').empty();
      jQuery('.instantvob_form_message').empty();

      if (!isValid) {
        jQuery('.instantvob_form_error').append(
          'Please correct the noted issues above before continuing.'
        );
        return;
      }

      const payload = convertFormToJSON(jQuery('#instantvob_form'));

      if (payload.instantvob_form_highlight !== '') {
        jQuery('.instantvob_form_message').append('automation not allowed');
        return;
      }

      // transform the date into a format InstantVOB will accept:
      // MM-DD-YYYY
      try {
        const dob = new Date(payload.instantvob_form_dob);

        payload.instantvob_form_dob = formatDate(dob);
      } catch (e) {
        console.error('Could not transform date', e);
      }

      const errorMessage =
        'There was a problem submitting your information, please try again later.';

      jQuery.ajax({
        url: instantvob_submit_ajax_script.ajaxurl,
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function (data) {
          if (
            data.response &&
            data.response.response &&
            data.response.response.code === 200
          ) {
            jQuery('.instantvob_form_message').append('Success!');
          } else {
            jQuery('.instantvob_form_error').append(errorMessage);
          }
          if (data.validation && !data.validation.success) {
            jQuery('.instantvob_form_error').append(
              ` (${data.validation['error-codes'].join(',')})`
            );
          }
        },
        error: function (response) {
          jQuery('.instantvob_form_error').append(
            'There was a problem submitting your information, please try again later.'
          );
        },
      });
    });
  });
})();
