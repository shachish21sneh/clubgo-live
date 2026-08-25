/**
 * ClubGo Frontend Interactive JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {
  // Mobile Nav Active State
  const currentPath = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.bottom-nav-item, .nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && (href === currentPath || (currentPath === '' && href === 'index.php'))) {
      link.classList.add('active');
    }
  });

  // Modal Open / Close Logic
  window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  };

  // Close modals when clicking backdrop
  document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', (e) => {
      if (e.target === backdrop) {
        backdrop.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });

  // User Dropdown Menu Toggle
  const userTrigger = document.querySelector('.user-profile-trigger');
  const userMenu = document.querySelector('.user-dropdown-menu');
  if (userTrigger && userMenu) {
    userTrigger.addEventListener('click', (e) => {
      e.stopPropagation();
      userMenu.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (!userTrigger.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove('active');
      }
    });
  }

  // City Picker Selector
  document.querySelectorAll('.city-card-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const cityId = this.dataset.cityId;
      const cityName = this.dataset.cityName;
      
      // Store in cookies/session
      document.cookie = `selected_city_id=${cityId}; path=/; max-age=2592000`;
      document.cookie = `selected_city_name=${encodeURIComponent(cityName)}; path=/; max-age=2592000`;
      localStorage.setItem('clubgo_city_id', cityId);
      localStorage.setItem('clubgo_city_name', cityName);

      // Update current page with city parameter or reload
      const url = new URL(window.location.href);
      url.searchParams.set('city', cityId);
      window.location.href = url.toString();
    });
  });

  // Live Search Suggestions
  const searchInput = document.getElementById('globalSearchInput');
  const searchSuggestions = document.getElementById('searchSuggestions');
  let searchTimeout = null;

  if (searchInput && searchSuggestions) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      const query = this.value.trim();
      if (query.length < 2) {
        searchSuggestions.classList.remove('active');
        searchSuggestions.innerHTML = '';
        return;
      }

      searchTimeout = setTimeout(() => {
        fetch(`auth_handler.php?action=search_suggest&q=${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(data => {
            if (data.results && data.results.length > 0) {
              let html = '';
              data.results.forEach(item => {
                const img = item.img || 'images/website/clubgoimg.webp';
                html += `
                  <a href="${item.url}" class="suggestion-item">
                    <img src="${img}" alt="${item.title}" class="suggestion-thumb">
                    <div>
                      <div style="font-weight:700; font-size:14px;">${item.title}</div>
                      <div style="font-size:12px; color:var(--text-muted);">${item.subtitle || ''} • <span class="badge badge-primary">${item.type}</span></div>
                    </div>
                  </a>
                `;
              });
              searchSuggestions.innerHTML = html;
              searchSuggestions.classList.add('active');
            } else {
              searchSuggestions.innerHTML = '<div style="padding:16px; text-align:center; color:var(--text-muted); font-size:13px;">No matching events or venues found.</div>';
              searchSuggestions.classList.add('active');
            }
          })
          .catch(() => {
            searchSuggestions.classList.remove('active');
          });
      }, 300);
    });

    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
        searchSuggestions.classList.remove('active');
      }
    });
  }

  // Bookmark Toggle Action
  document.querySelectorAll('.bookmark-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const eid = this.dataset.eid;
      const vid = this.dataset.vid;
      const type = eid ? 'event' : 'venue';
      const id = eid || vid;

      fetch('auth_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'toggle_bookmark', type, id })
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'need_login') {
          openModal('authModal');
        } else if (res.status === 'success') {
          if (res.bookmarked) {
            btn.classList.add('active');
          } else {
            btn.classList.remove('active');
          }
        }
      })
      .catch(err => console.error(err));
    });
  });

  // Auth Modal Tab Switcher (Login / Register)
  const authTabBtns = document.querySelectorAll('.auth-tab-btn');
  const authForms = document.querySelectorAll('.auth-form-panel');
  authTabBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      authTabBtns.forEach(b => b.classList.remove('active'));
      authForms.forEach(f => f.style.display = 'none');
      
      this.classList.add('active');
      const targetId = this.dataset.target;
      const targetForm = document.getElementById(targetId);
      if (targetForm) targetForm.style.display = 'block';
    });
  });

  // AJAX Login Form Submission
  const userLoginForm = document.getElementById('userLoginForm');
  if (userLoginForm) {
    userLoginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const mobile = this.querySelector('[name="mobile"]').value.trim();
      const password = this.querySelector('[name="password"]').value;
      const submitBtn = this.querySelector('button[type="submit"]');
      const errorMsg = document.getElementById('loginErrorMsg');

      submitBtn.disabled = true;
      submitBtn.innerText = 'Signing in...';
      if (errorMsg) errorMsg.style.display = 'none';

      fetch('auth_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'login', mobile, password })
      })
      .then(res => res.json())
      .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Sign In';
        if (data.status === 'success') {
          window.location.reload();
        } else {
          if (errorMsg) {
            errorMsg.innerText = data.message || 'Invalid credentials.';
            errorMsg.style.display = 'block';
          } else {
            alert(data.message || 'Invalid credentials');
          }
        }
      })
      .catch(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Sign In';
        alert('An error occurred during login. Please try again.');
      });
    });
  }

  // AJAX Register Form Submission
  const userRegisterForm = document.getElementById('userRegisterForm');
  if (userRegisterForm) {
    userRegisterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const name = this.querySelector('[name="name"]').value.trim();
      const email = this.querySelector('[name="email"]').value.trim();
      const mobile = this.querySelector('[name="mobile"]').value.trim();
      const password = this.querySelector('[name="password"]').value;
      const referral = this.querySelector('[name="referral"]')?.value.trim() || '';
      const submitBtn = this.querySelector('button[type="submit"]');
      const errorMsg = document.getElementById('registerErrorMsg');

      submitBtn.disabled = true;
      submitBtn.innerText = 'Creating account...';
      if (errorMsg) errorMsg.style.display = 'none';

      fetch('auth_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'register', name, email, mobile, password, referral })
      })
      .then(res => res.json())
      .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Create Account';
        if (data.status === 'success') {
          window.location.reload();
        } else {
          if (errorMsg) {
            errorMsg.innerText = data.message || 'Registration failed.';
            errorMsg.style.display = 'block';
          } else {
            alert(data.message || 'Registration failed.');
          }
        }
      })
      .catch(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Create Account';
        alert('An error occurred during registration. Please try again.');
      });
    });
  }

  // Event Detail Tabs
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const parent = this.closest('.detail-main') || document;
      parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      parent.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      
      this.classList.add('active');
      const targetId = this.dataset.tab;
      const targetContent = document.getElementById(targetId);
      if (targetContent) targetContent.classList.add('active');
    });
  });

  // Hero Carousel Auto Play & Manual Navigation
  const heroSlider = document.querySelector('.hero-slider-track');
  const heroSlides = document.querySelectorAll('.hero-slide');
  let currentSlide = 0;
  
  if (heroSlider && heroSlides.length > 1) {
    const updateSlide = (idx) => {
      currentSlide = (idx + heroSlides.length) % heroSlides.length;
      heroSlider.style.transform = `translateX(-${currentSlide * 100}%)`;
    };

    const nextBtn = document.getElementById('heroNextBtn');
    const prevBtn = document.getElementById('heroPrevBtn');
    if (nextBtn) nextBtn.addEventListener('click', () => updateSlide(currentSlide + 1));
    if (prevBtn) prevBtn.addEventListener('click', () => updateSlide(currentSlide - 1));

    setInterval(() => {
      updateSlide(currentSlide + 1);
    }, 6000);
  }
});
