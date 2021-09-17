setupJoinForm()

function setupJoinForm() {
  const joinForm = document.getElementById('join')
  if (joinForm == null)
    return

  let enabled = true;

  enableForm = (state) => {
    enabled = state
    joinForm.querySelector('.btn-primary').disabled = !state
  }

  success = () => {
    joinForm.querySelector('.btn-primary').classList.add('hide')
    joinForm.querySelector('.msg-success').classList.remove('hide')
  }

  failure = (err) => {
    const errMsg = joinForm.querySelector('.msg-error')

    errMsg.textContent = err
    errMsg.classList.remove('hide')

    grecaptcha.reset()

    enableForm(true)
  }

  joinForm.addEventListener('submit', (e) => {
    e.preventDefault()

    if (!enabled)
      return

    enableForm(false)
    joinForm.querySelector('.msg-block').classList.add('hide')

    submitForm(joinForm).then(success, failure)
  })
}

const defaultError = 'Something went wrong, please try again later'

function submitForm(form) {
  return fetch(form.action, {
    method: form.method,
    redirect: 'error',
    cache: 'no-cache',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(readForm(form))
  }).then(
    (resp) => resp.ok ? null : failResponse(resp),
    (err) => {
      console.error(err)
      return Promise.reject(defaultError)
    }
  )
}

function failResponse(resp) {
  if (resp.status == 422)
    return resp.json().then(
      (data) => Promise.reject(data.error),
      (err) => {
        console.error(err)
        return Promise.reject(defaultError)
      }
    )
  else
    return Promise.reject(defaultError)
}

function readForm(form) {
  const result = {};

  form.querySelectorAll('input,textarea').forEach((el) => {
    result[el.name] = el.type === 'checkbox' ? el.checked : el.value
  })

  return result
}

