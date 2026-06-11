document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.ez-field-edit__error').forEach(function (el) {
    el.style.display = 'none'
  })

  document.addEventListener('click', function (e) {
    const editTerms = e.target.closest('#edit-terms')
    if (editTerms) {
      e.preventDefault()
      const currTerm = editTerms.closest('tr').querySelector('.term')
      const currSynonyms = editTerms.closest('tr').querySelector('.synonyms')
      const texts = []
      const listSynonyms = currSynonyms.querySelector('ul')
      listSynonyms.querySelectorAll('li').forEach(function (li) {
        texts.push(li.textContent.trim())
      })

      const inputTerm = document.createElement('input')
      inputTerm.type = 'text'
      inputTerm.name = 'term'
      inputTerm.disabled = true
      inputTerm.className = 'form-control required update-term'
      inputTerm.value = currTerm.querySelector('label').innerHTML

      const inputSynonyms = document.createElement('input')
      inputSynonyms.type = 'text'
      inputSynonyms.name = 'synonyms'
      inputSynonyms.className = 'form-control required update-synonyms'
      inputSynonyms.value = texts.join(',')

      currTerm.querySelector('label').style.display = 'none'
      currTerm.appendChild(inputTerm)
      listSynonyms.style.display = 'none'
      currSynonyms.appendChild(inputSynonyms)
      editTerms.closest('td').querySelector('.valid-update-terms').style.display = ''
      editTerms.closest('td').querySelector('.cancel-update-terms').style.display = ''
      editTerms.style.display = 'none'
    }

    const cancelUpdate = e.target.closest('.cancel-update-terms')
    if (cancelUpdate) {
      e.preventDefault()
      const currTerm = cancelUpdate.closest('tr').querySelector('.term')
      const currSynonyms = cancelUpdate.closest('tr').querySelector('.synonyms')
      cancelUpdate.closest('td').querySelector('.valid-update-terms').style.display = 'none'
      cancelUpdate.closest('td').querySelector('#edit-terms').style.display = ''
      currTerm.querySelector('label').style.display = ''
      currSynonyms.querySelector('ul').style.display = ''

      const updateTerm = currTerm.querySelector('.update-term')
      if (updateTerm) updateTerm.remove()
      const updateSynonyms = currSynonyms.querySelector('.update-synonyms')
      if (updateSynonyms) updateSynonyms.remove()
      cancelUpdate.style.display = 'none'
    }

    const validUpdate = e.target.closest('.valid-update-terms')
    if (validUpdate) {
      e.preventDefault()
      let error = false
      validUpdate.closest('tr').querySelectorAll('.update-term, .update-synonyms').forEach(function (input) {
        if (input.value === '') {
          input.classList.add('is-invalid')
          error = true
        } else {
          input.classList.remove('is-invalid')
        }
      })
      if (!error) {
        validUpdate.closest('tr').querySelectorAll('input:disabled').forEach(function (input) {
          input.removeAttribute('disabled')
        })

        const formData = Array.from(validUpdate.closest('tr').querySelectorAll('.update-term, .update-synonyms'))
            .map(input => encodeURIComponent(input.name) + '=' + encodeURIComponent(input.value))
            .join('&')
        const actionUrl = validUpdate.getAttribute('href')

        fetch(actionUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData
        })
            .then(response => response.text())
            .then(function (data) {
              document.querySelector('.page-list-terms').innerHTML = data
            })
      }
    }

    const validUpdateWords = e.target.closest('.valid-update-words')
    if (validUpdateWords) {
      e.preventDefault()
      let error = false
      const textarea = validUpdateWords.closest('tr').querySelector('textarea')
      if (textarea.value === '') {
        textarea.classList.add('is-invalid')
        error = true
      } else {
        textarea.classList.remove('is-invalid')
      }
      if (!error) {
        const formData = encodeURIComponent(textarea.name) + '=' + encodeURIComponent(textarea.value)
        const actionUrl = validUpdateWords.getAttribute('href')

        fetch(actionUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData
        })
            .then(response => response.text())
            .then(function (data) {
              document.querySelector('.page-list-words').innerHTML = data
            })
      }
    }

    const btnDanger = e.target.closest('#delete-terms-modal .btn-danger')
    if (btnDanger) {
      const clickTarget = document.querySelector(btnDanger.getAttribute('data-click'))
      if (clickTarget) clickTarget.click()
    }

    const deleteTermsDelete = e.target.closest('#delete-terms-delete')
    if (deleteTermsDelete) {
      e.preventDefault()
      const formData = Array.from(document.querySelectorAll('.terms-to-delete:checked'))
          .map(checkbox => encodeURIComponent(checkbox.name) + '=' + encodeURIComponent(checkbox.value))
          .join('&')
      const actionUrl = deleteTermsDelete.getAttribute('href')

      fetch(actionUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
      })
          .then(response => response.text())
          .then(function (data) {
            const modalEl = document.getElementById('delete-terms-modal')
            if (modalEl) {
              const modal = bootstrap.Modal.getInstance(modalEl)
              if (modal) {
                modal.hide()
              }
            }
            document.querySelector('.page-list-terms').innerHTML = data
          })
    }

    const wordsModalBtn = e.target.closest('#delete-words-modal .btn-danger')
    if (wordsModalBtn) {
      const clickTarget = document.querySelector(wordsModalBtn.getAttribute('data-click'))
      if (clickTarget) clickTarget.click()
    }

    const deleteWordsDelete = e.target.closest('#delete-words-delete')
    if (deleteWordsDelete) {
      e.preventDefault()
      const formData = Array.from(document.querySelectorAll('.words-to-delete:checked'))
          .map(checkbox => encodeURIComponent(checkbox.name) + '=' + encodeURIComponent(checkbox.value))
          .join('&')
      const actionUrl = deleteWordsDelete.getAttribute('href')

      fetch(actionUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
      })
          .then(response => response.text())
          .then(function (data) {
            const modalEl = document.getElementById('delete-words-modal')
            if (modalEl) {
              const modal = bootstrap.Modal.getInstance(modalEl)
              if (modal) {
                modal.hide()
              }
            }
            document.querySelector('.page-list-words').innerHTML = data
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

  document.addEventListener('change', function (e) {
    if (e.target.classList.contains('terms-to-delete')) {
      const deleteBtn = document.getElementById('delete-terms')
      if (checkAny('termsToDelete[]')) {
        deleteBtn.removeAttribute('disabled')
      } else {
        deleteBtn.setAttribute('disabled', 'disabled')
      }
    }

    if (e.target.classList.contains('words-to-delete')) {
      const deleteBtn = document.getElementById('delete-words')
      if (checkAny('wordsToDelete[]')) {
        deleteBtn.removeAttribute('disabled')
      } else {
        deleteBtn.setAttribute('disabled', 'disabled')
      }
    }
  })
})
