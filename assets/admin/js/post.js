jQuery(function($) {
    const ID_FIELD_SELECTOR_REDIRECT = '#short-link-redirect'
    const isValidURL = (url) => {
        const pattern = /(https:\/\/www\.|http:\/\/www\.|https:\/\/|http:\/\/)?[a-zA-Z]{2,}(\.[a-zA-Z]{2,})(\.[a-zA-Z]{2,})?\/[a-zA-Z0-9]{2,}|((https:\/\/www\.|http:\/\/www\.|https:\/\/|http:\/\/)?[a-zA-Z]{2,}(\.[a-zA-Z]{2,})(\.[a-zA-Z]{2,})?)|(https:\/\/www\.|http:\/\/www\.|https:\/\/|http:\/\/)?[a-zA-Z0-9]{2,}\.[a-zA-Z0-9]{2,}\.[a-zA-Z0-9]{2,}(\.[a-zA-Z0-9]{2,})?/g
        return pattern.test(url)
    }

    $(ID_FIELD_SELECTOR_REDIRECT).keyup(function () {
        checkValidURL($(this).val())
    })

    const checkValidURL = (url) => {
        const $buttonSave = $('#publishing-action [name="save"]')
        isValidURL(url) ? $buttonSave.removeClass('disabled') : $buttonSave.addClass('disabled')
    }

    const field = $(ID_FIELD_SELECTOR_REDIRECT)
    if (field.length > 0) checkValidURL(field.val())
})
