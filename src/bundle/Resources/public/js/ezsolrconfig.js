/*
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

jQuery(function ($) {
  $('.ez-field-edit__error').hide()
  $(document).on('click', '#edit-terms', function (e) {
    e.preventDefault()
    const $this = $(this)
    const currTerm = $this.closest('tr').find('.term')
    const currSynonyms = $this.closest('tr').find('.synonyms')
    const texts = []
    const listSynonyms = $(currSynonyms).find('ul')
    listSynonyms.find('li').each(function () {
      texts.push($(this).text().trim())
    })
    const inputTerm = $('<input />', {
      'type': 'text',
      'name': 'term',
      'disabled': 'disabled',
      'class': 'form-control required update-term',
      'value': currTerm.find('label').html()
    })
    const inputSynonyms = $('<input />', {
      'type': 'text',
      'name': 'synonyms',
      'class': 'form-control required update-synonyms',
      'value': texts.join(',')
    })
    $(currTerm).find('label').hide()
    $(currTerm).append(inputTerm)
    listSynonyms.hide()
    $(currSynonyms).append(inputSynonyms)
    $this.closest('td').find('.valid-update-terms').show()
    $this.closest('td').find('.cancel-update-terms').show()
    $this.hide()
  })

  $(document).on('click', '.cancel-update-terms', function (e) {
    e.preventDefault()
    const $this = $(this)
    const currTerm = $this.closest('tr').find('.term')
    const currSynonyms = $this.closest('tr').find('.synonyms')
    $this.closest('td').find('.valid-update-terms').hide()
    $this.closest('td').find('#edit-terms').show()
    currTerm.find('label').show()
    currSynonyms.find('ul').show()
    currTerm.find('.update-term').remove()
    currSynonyms.find('.update-synonyms').remove()
    $this.hide()
  })

  $(document).on('click', '.valid-update-terms', function (e) {
    e.preventDefault()
    const $this = $(this)
    let error = false
    $this.closest('tr').find('.update-term, .update-synonyms').each(function () {
      if ($(this).val() === '') {
        $(this).addClass('is-invalid')
        error = true
      } else {
        $(this).removeClass('is-invalid')
      }
    })
    if (!error) {
      $this.closest('tr').find(':input:disabled').removeAttr('disabled')

      const formData = $this.closest('tr').find('.update-term, .update-synonyms').serialize()
      const actionUrl = $this.attr('href')

      $.ajax({
        url: actionUrl,
        type: 'post',
        dataType: 'html',
        data: formData,
        success: function (data) {
          $('.page-list-terms').html(data)
        }
      })
    }
  })
  $(document).on('click', '.valid-update-words', function (e) {
    e.preventDefault()
    const $this = $(this)
    let error = false
    const textarea = $this.closest('tr').find('textarea')
    if (textarea.val() === '') {
      textarea.addClass('is-invalid')
      error = true
    } else {
      textarea.removeClass('is-invalid')
    }
    if (!error) {
      const formData = textarea.serialize()
      const actionUrl = $this.attr('href')

      $.ajax({
        url: actionUrl,
        type: 'post',
        dataType: 'html',
        data: formData,
        success: function (data) {
          $('.page-list-words').html(data)
        }
      })
    }
  })
  function checkAny (name) {
    const chkArr = document.getElementsByName(name)
    for (let k = 0; k < chkArr.length; k++) {
      if (chkArr[k].checked === true) {
        return true
      }
    }
    return false
  }

  $(document).on('change', '.terms-to-delete', function () {
    checkAny('termsToDelete[]') ? $('#delete-terms').removeAttr('disabled') : $('#delete-terms').attr('disabled', 'disabled')
  })

  $(document).on('click', '#delete-terms-modal .btn-danger', function () {
    $($(this).attr('data-click')).click()
  })

  $(document).on('click', '#delete-terms-delete', function (e) {
    e.preventDefault()
    const $this = $(this)
    const formData = $('.terms-to-delete').serialize()
    const actionUrl = $this.attr('href')

    $.ajax({
      url: actionUrl,
      type: 'post',
      dataType: 'html',
      data: formData,
      success: function (data) {
        $('#delete-terms-modal').modal('hide')
        $('.modal-backdrop').remove()
        $('.page-list-terms').html(data)
      }
    })
  })

  $(document).on('change', '.words-to-delete', function () {
    checkAny('wordsToDelete[]') ? $('#delete-words').removeAttr('disabled') : $('#delete-words').attr('disabled', 'disabled')
  })

  $(document).on('click', '#delete-words-modal .btn-danger', function () {
    $($(this).attr('data-click')).click()
  })

  $(document).on('click', '#delete-words-delete', function (e) {
    e.preventDefault()
    const $this = $(this)
    const formData = $('.words-to-delete').serialize()
    const actionUrl = $this.attr('href')
    $.ajax({
      url: actionUrl,
      type: 'post',
      dataType: 'html',
      data: formData,
      success: function (data) {
        $('#delete-words-modal').modal('hide')
        $('.modal-backdrop').remove()
        $('.page-list-words').html(data)
      }
    })
  })
})
