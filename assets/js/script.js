// Updated version of your JavaScript file to fully integrate with backend data and session-based login/signup/logout

$(document).ready(function () {

  console.log("✅ script.js loaded");

  //:::::::::::::::::::::::::::::::: UPLOADING A MEME ::::::::::::::::::::::::::::::::\\
  $('#upload-form').submit(function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
      url: 'php/upload_meme.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log('Upload response:', response);
        if (response.status === 'success') {
          $('#upload-modal').modal('close');
          showAnimatedToast('Meme uploaded successfully!', 'check');
          $('#upload-form')[0].reset();
          loadMemes();
        } else {
          showAnimatedToast(response.message || 'Upload failed!', 'error');
        }
      },
      error: function (xhr) {
        console.error('AJAX error:', xhr.responseText);
        showAnimatedToast('An error occurred during upload.', 'error');
      }
    });
  });

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

  let currentUserID = null;


function checkSession() {
  console.log("📡 Running checkSession()");

  $.get('php/session_status.php', function (response) {
    console.log("✅ Session response:", response);
    if (response.loggedIn) {
      currentUserID = parseInt(response.userID);
      console.log("✔ Logged in as ID:", currentUserID);
      updateAuthUI(true, response.username);
    } else {
      console.log("❌ Not logged in");
      updateAuthUI(false);
    }

    loadMemes();
  }, 'json');
}

  checkSession();
  M.AutoInit(); // OR:
  $('.modal').modal();

  //:::::::::::::::::::::::::::::::: LOGGING IN ::::::::::::::::::::::::::::::::\\
  $('#login-form').submit(function (e) {
    e.preventDefault();
    const email = $('#login-email').val();
    const password = $('#login-password').val();

    $.post('php/login.php', { email, password }, function (response) {
      if (response.status === 'success') {
        currentUserID = parseInt(response.userID);
        updateAuthUI(true, response.username);
        $('#login-modal').modal('close');
        showAnimatedToast('Logged in successfully!', 'check');
        loadMemes();
      } else {
        showAnimatedToast(response.message, 'error');
      }
    }, 'json');
  });

  //:::::::::::::::::::::::::::::::: SIGNING UP ::::::::::::::::::::::::::::::::\\
  $('#signup-form').submit(function (e) {
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
    }, function (response) {
      console.log('Signup response:', response); // 🔍 add this temporarily
      if (response.status === 'success') {
        currentUserID = parseInt(response.userID);
        updateAuthUI(true, username);
        $('#signup-modal').modal('close');
        showAnimatedToast('Signup successful!', 'check');
      } else {
        showAnimatedToast(response.message, 'error');
      }
    }, 'json');
  });

  //:::::::::::::::::::::::::::::::: LOGGING OUT ::::::::::::::::::::::::::::::::\\
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

  //:::::::::::::::::::::::::::::::: LOADING MEMES ::::::::::::::::::::::::::::::::\\
  function loadMemes() {
    $('#meme-grid').css('opacity', 0).empty();

    $.get('php/fetch_memes.php', function (response) {
      if (response.status === 'success' && response.data.length > 0) {
        response.data.forEach((meme, index) => {
          const isOwner = currentUserID === parseInt(meme.userID);

          const memeCard = `
              <div class="meme-card" data-meme-id="${meme.memeID}" style="animation-delay: ${index * 0.1}s">
                <div class="meme-image-container">
                  <img src="${meme.file_path}" alt="${meme.caption}" class="meme-image">
                </div>
                <div class="meme-actions">
                  ${meme.caption ? `
                    <h6 class="meme-title" title="${meme.caption}">${meme.caption}</h6>
                  ` : ''}
                  <div class="meme-user">
                    @${meme.username} • ${formatDate(meme.upload_date)}
                  </div>
                  <div class="meme-reactions">
                    <button class="reaction-btn like-btn ${meme.has_liked ? 'active' : ''}" data-action="like">
                      <i class="material-icons">thumb_up</i>
                      <span class="reaction-count">${meme.like_count ?? 0}</span>
                    </button>
                    <button class="reaction-btn upvote-btn ${meme.has_upvoted ? 'active' : ''}" data-action="upvote">
                      <i class="material-icons">arrow_upward</i>
                      <span class="reaction-count">${meme.upvote_count ?? 0}</span>
                    </button>
                    <button class="reaction-btn share-btn modal-trigger" data-target="share-modal" data-action="share">
                      <i class="material-icons">share</i>
                    </button>
                    <button class="reaction-btn download-btn" data-action="download">
                      <i class="material-icons">file_download</i>
                    </button>
                  </div>
                </div>
              </div>
            `;

          $('#meme-grid').append(memeCard);
        });

        $('#meme-grid').animate({ opacity: 1 }, 300);
        $('.like-btn').click(handleReaction);

      } else {
        $('#meme-grid').html(`
            <div class="empty-state center-align">
              <i class="material-icons large">sentiment_dissatisfied</i>
              <h5>No memes found</h5>
              <p>Be the first to upload a meme and start the fun!</p>
              ${isLoggedIn ? '<a href="#" class="btn waves-effect waves-light modal-trigger" data-target="upload-modal">Upload Meme</a>' : ''}
            </div>
          `).animate({ opacity: 1 }, 300);
      }
    }, 'json');
  }

  function formatDate(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function handleReaction() {
    const button = $(this);
    const memeId = button.closest('.meme-card').data('meme-id');
    const action = button.data('action');

    console.log("🧪 Reacting:", {
      memeId,
      action,
      userID: currentUserID
    });
    console.log(`ID: ${currentUserID}`);

    $.ajax({
      url: 'php/react.php',
      method: 'POST',
      data: JSON.stringify({
        id: memeId,
        action: action,
        userID: currentUserID
      }),
      contentType: 'application/json',
      dataType: 'json',
      success: function (response) {
  const countEl = button.find('.reaction-count');

  if (response.removed) {
    button.removeClass('active');
  } else if (response.added) {
    button.addClass('active');
  }

  // Update count regardless
  if (response.like_count !== undefined) {
    countEl.text(response.like_count);
  } else if (response.upvote_count !== undefined) {
    countEl.text(response.upvote_count);
  }
}
,
      error: function () {
        showAnimatedToast('Reaction failed. Try again.', 'error');
      }
    });
  }

  function showAnimatedToast(message, icon) {
    $('.toast').remove(); // Remove any existing toasts before showing a new one
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

  // SHARE BUTTON LOGIC
  $(document).on('click', '.share-btn', function () {
    console.log("Share button clicked");
    const memeCard = $(this).closest('.meme-card');
    const imgSrc = memeCard.find('img').attr('src');
    const fullURL = `${window.location.origin}${imgSrc.startsWith('/') ? '' : '/'}${imgSrc}`;

    // Fill modal input
    $('#share-link').val(fullURL);
    M.updateTextFields();
    $('#share-modal').modal('open');

    // Copy to clipboard
    $('#copy-link-btn').off('click').on('click', function () {
      navigator.clipboard.writeText(fullURL).then(() => {
        M.toast({ html: '✅ Link copied to clipboard!', classes: 'green' });
      }).catch(() => {
        M.toast({ html: '❌ Failed to copy.', classes: 'red' });
      });
    });

    // Twitter
    $('#share-twitter-btn').off('click').on('click', function () {
      const tweetText = encodeURIComponent("Check out this meme from ZedMemes!");
      window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(fullURL)}&text=${tweetText}`, '_blank');
    });

    // WhatsApp & Gmail
    const whatsappBtn = $('<a>', {
      href: `https://api.whatsapp.com/send?text=${encodeURIComponent('Check this meme: ' + fullURL)}`,
      target: '_blank',
      class: 'btn green lighten-1 waves-effect waves-light',
      html: '<i class="material-icons left">chat</i>WhatsApp'
    });

    const gmailBtn = $('<a>', {
      href: `mailto:?subject=ZedMemes Meme&body=Hey, thought you’d enjoy this meme: ${fullURL}`,
      class: 'btn red lighten-1 waves-effect waves-light',
      html: '<i class="material-icons left">email</i>Gmail'
    });

    $('#extra-share').empty(); // Clear old share buttons

    $('#extra-share').append($('<div class="col s6">').append(whatsappBtn));
    $('#extra-share').append($('<div class="col s6">').append(gmailBtn));
  });

});
