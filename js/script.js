const qs = (selector, scope = document) => scope.querySelector(selector)
const qsa = (selector, scope = document) => Array.from(scope.querySelectorAll(selector))

const togglePassword = (button, input) => {
  const icon = button.querySelector("img")
  if (!icon) return
  
  button.addEventListener("click", () => {
    const isHidden = input.type === "password"
    input.type = isHidden ? "text" : "password"
    icon.src = isHidden ? "icon/eye-slash.png" : "icon/eye.png"
    icon.alt = isHidden ? "Hide password" : "Show password"
  })
}

const showMessage = (element, message) => {
  if (!element) return
  element.textContent = message
  element.classList.remove("hidden")
}

const clearMessage = (element) => {
  if (!element) return
  element.textContent = ""
  element.classList.add("hidden")
}

const handleLogin = () => {
  const form = qs("#login-form")
  if (!form) return

  const emailInput = qs("#login-email")
  const passwordInput = qs("#login-password")
  const errorBox = qs("#login-error")
  const submitBtn = qs("#login-submit")
  const toggleBtn = qs("#login-toggle")

  togglePassword(toggleBtn, passwordInput)

  form.addEventListener("submit", (e) => {
    clearMessage(errorBox)

    const email = emailInput.value.trim()
    const password = passwordInput.value.trim()

    if (!email || !password) {
      e.preventDefault()
      showMessage(errorBox, "Please enter your email and password.")
      return
    }

    // If validation passes, allow form to submit to PHP
    submitBtn.disabled = true
    submitBtn.textContent = "Signing in..."
  })
}

const handleRegister = () => {
  const form = qs("#register-form")
  if (!form) return

  const fields = {
    fullName: qs("#reg-fullname"),
    username: qs("#reg-username"),
    email: qs("#reg-email"),
    password: qs("#reg-password"),
    confirmPassword: qs("#reg-confirm"),
    terms: qs("#reg-terms"),
  }

  const errorBox = qs("#register-error")
  const submitBtn = qs("#register-submit")
  const togglePasswordBtn = qs("#register-toggle")
  const toggleConfirmBtn = qs("#register-toggle-confirm")

  togglePassword(togglePasswordBtn, fields.password)
  togglePassword(toggleConfirmBtn, fields.confirmPassword)

  form.addEventListener("submit", (e) => {
    clearMessage(errorBox)

    if (!fields.terms.checked) {
      e.preventDefault()
      showMessage(errorBox, "Please agree to the Terms of Service and Privacy Policy.")
      return
    }

    if (fields.password.value.length < 8) {
      e.preventDefault()
      showMessage(errorBox, "Password must be at least 8 characters long.")
      return
    }

    if (fields.password.value !== fields.confirmPassword.value) {
      e.preventDefault()
      showMessage(errorBox, "Passwords do not match.")
      return
    }

    // If validation passes, allow form to submit to PHP
    submitBtn.disabled = true
    submitBtn.textContent = "Creating account..."
  })
}

document.addEventListener("DOMContentLoaded", () => {
  handleLogin()
  handleRegister()
})

