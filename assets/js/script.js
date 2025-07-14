// Updated version of your JavaScript file to fully integrate with backend data and session-based login/signup/logout

$(document).ready(function () {
    M.AutoInit();
    $('.modal').modal({ inDuration: 300, outDuration: 200, startingTop: '10%', endingTop: '10%' });
    $('.sidenav').sidenav();
    $('.fixed-action-btn').floatingActionButton();
  
    const darkModeToggle = $('#dark-mode-btn, #mobile-dark-mode-btn');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
  
    if (localStorage.getItem('darkMode') === 'enabled' ||
      (localStorage.getItem('darkMode') === null && prefersDarkScheme.matches)) {
      enableDarkMode();
    }
  
    darkModeToggle.click(function (e) {
      e.preventDefault();
      if ($('body').hasClass('dark-theme')) {
        disableDarkMode();
      } else {
        enableDarkMode();
      }
    });
  
    function enableDarkMode() {
      $('body').addClass('dark-theme');
      darkModeToggle.find('i').text('brightness_7');
      localStorage.setItem('darkMode', 'enabled');
    }
  
    function disableDarkMode() {
      $('body').removeClass('dark-theme');
      darkModeToggle.find('i').text('brightness_4');
      localStorage.setItem('darkMode', 'disabled');
    }
  
    prefersDarkScheme.addListener(e => {
      if (localStorage.getItem('darkMode') === null) {
        if (e.matches) {
          enableDarkMode();
        } else {
          disableDarkMode();
        }
      }
    });
  
    checkSession();
    loadMemes();
  
    function checkSession() {
      $.get('php/session_status.php', function (response) {
        if (response.loggedIn) {
          updateAuthUI(true, response.username);
        } else {
          updateAuthUI(false);
        }
      }, 'json');
    }
  
    $('#login-form').submit(function (e) {
      e.preventDefault();
      const email = $('#login-email').val();
      const password = $('#login-password').val();
  
      $.post('php/login.php', { email, password }, function (response) {
        if (response.status === 'success') {
          updateAuthUI(true, response.username);
          $('#login-modal').modal('close');
          showAnimatedToast('Logged in successfully!', 'check');
          loadMemes();
        } else {
          showAnimatedToast(response.message, 'error');
        }
      }, 'json');
    });
  
    $('#signup-form').submit(function(e) {
      e.preventDefault();
    
      const username = $('#signup-username').val();
      const email = $('#signup-email').val();
      const password = $('#signup-password').val();
      const confirm = $('#signup-confirm-password').val();
    
      if (password !== confirm) {
        showAnimatedToast('Passwords do not match!', 'error');
        return;
      }

      console.log('Signup values:', username, email, password);
    
      $.post('php/signup.php', {
        username,
        email,
        password
      }, function(response) {
        console.log('Signup response:', response); // 🔍 add this temporarily
        if (response.status === 'success') {
          updateAuthUI(true, username);
          $('#signup-modal').modal('close');
          showAnimatedToast('Signup successful!', 'check');
        } else {
          showAnimatedToast(response.message, 'error');
        }
      }, 'json');
    });
    
  
    $('#logout-link, #mobile-logout-link').click(function (e) {
      e.preventDefault();
      $.get('php/logout.php', function () {
        updateAuthUI(false);
        showAnimatedToast('Logged out successfully!', 'exit_to_app');
        loadMemes();
        //location.reload();
      });
    });
  
    function updateAuthUI(isLoggedIn, username = '') {
      if (isLoggedIn) {
        $('#auth-buttons, #mobile-auth-buttons').hide();
        $('#user-buttons, #mobile-user-buttons, #mobile-fab').show();

        if (!$('.brand-logo').next().length && username !== '') {
          $('.brand-logo').after(`
            <ul class="right hide-on-med-and-down">
              <li><a class="btn-flat">👤 ${username}</a></li>
            </ul>
          `);
        }
      } else {
        $('#auth-buttons, #mobile-auth-buttons').show();
        $('#user-buttons, #mobile-user-buttons, #mobile-fab').hide();
        $('.right li:has(.btn-flat)').remove();
      }
    }
  
    function loadMemes() {
      $('#meme-grid').css('opacity', 0).empty();
      $.get('php/fetch_memes.php', function (response) {
        if (response.status === 'success' && response.data.length > 0) {
          response.data.forEach((meme, index) => {
            const memeCard = `
              <div class="meme-card" data-meme-id="${meme.id}" style="animation-delay: ${index * 0.1}s">
                <div class="meme-image-container">
                  <img src="${meme.file_path}" alt="${meme.caption}" class="meme-image">
                </div>
                <div class="meme-actions">
                  <h6 class="meme-title" title="${meme.caption}">${meme.caption || 'Untitled Meme'}</h6>
                  <div class="meme-user">@${meme.username} • ${formatDate(meme.created_at)}</div>
                  <div class="meme-reactions">
                    <button class="reaction-btn like-btn" data-action="like">
                      <i class="material-icons">thumb_up</i>
                      <span class="reaction-count">${meme.like_count}</span>
                    </button>
                  </div>
                </div>
              </div>
            `;
            $('#meme-grid').append(memeCard);
          });
          $('#meme-grid').animate({ opacity: 1 }, 300);
          $('.modal-trigger').modal();
          $('.like-btn').click(handleReaction);
        } else {
          $('#meme-grid').html('<div class="empty-state center-align"><i class="material-icons large">sentiment_dissatisfied</i><h5>No memes found</h5><p>Be the first to upload a meme!</p></div>').animate({ opacity: 1 }, 300);
        }
      });
    }
  
    function formatDate(timestamp) {
      const date = new Date(timestamp);
      return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
  
    function handleReaction() {
      const button = $(this);
      const memeId = button.closest('.meme-card').data('meme-id');
      $.post('php/react.php', { memeID: memeId, reaction: 'like' }, function (response) {
        if (response.status === 'success') {
          button.find('.reaction-count').text(response.like_count);
          button.toggleClass('active');
        } else {
          showAnimatedToast('Please log in to like memes', 'error');
        }
      }, 'json');
    }
  
    function showAnimatedToast(message, icon) {
      const toastHTML = `
        <div class="toast">
          <div class="toast-content" style="display: flex; align-items: center;">
            <i class="material-icons" style="margin-right: 10px;">${icon}</i>
            ${message}
          </div>
        </div>
      `;
      M.toast({ html: toastHTML, classes: 'animated fadeInUp', displayLength: 3000 });
    }
  });
  