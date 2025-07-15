<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZedMemes - Meme Sharing Platform</title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="light-theme">
    <!-- Navigation -->
    <nav>
        <!--<?php if (isset($_SESSION['username'])): ?>
        <li><span class="white-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span></li>
        <?php endif; ?>-->
        <div class="nav-wrapper container">
            <a href="#" class="brand-logo">ZedMemes</a>
            <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
            <!-- This is always here -->
            <li>
                <a id="welcome-msg" class="btn-flat" style="display: none;">
                <i class="material-icons left">account_circle</i>
                <span id="username-label"></span>
                </a>
            </li>
            <li id="auth-buttons">
                <a href="#" class="btn-flat modal-trigger" data-target="login-modal">Login</a>
                <a href="#" class="btn modal-trigger" data-target="signup-modal">Sign Up</a>
            </li>
            <li id="user-buttons" style="display: none;">
                <a href="#" class="btn green modal-trigger" id="upload-btn">
                <i class="material-icons left">add</i> Post Meme
                </a>
                <a href="#" class="btn red" id="logout-link">Logout</a>
            </li>
            </ul>

            <!--<ul class="right hide-on-med-and-down">
                <li>
                    <a class="waves-effect waves-light btn-flat dark-mode-toggle" id="dark-mode-btn">
                        <i class="material-icons left">brightness_4</i>
                        <span class="hide-on-small-only">Dark Mode</span>
                    </a>
                </li>
                <?php if (!isset($_SESSION['user'])):
                ?>
                <li id="auth-buttons">
                    <a href="#" class="btn-flat waves-effect waves-light modal-trigger" data-target="login-modal">Login</a>
                    <a href="#" class="btn waves-effect waves-light modal-trigger" data-target="signup-modal">Sign Up</a>
                </li>
                <?php endif; ?>

                <?php if (!isset($_SESSION['user'])):?>
                <li id="user-buttons" style="display: none;">
                </li>
                <?php endif; ?>
                    <a href="#" class="btn waves-effect waves-light green darken-1 modal-trigger" id="upload-btn">
                        <i class="material-icons left">add</i>
                        <span class="hide-on-small-only">Post Meme</span>
                    </a>
                    <a href="#" class="btn waves-effect waves-light red darken-1" id="logout-link">Logout</a>
                </li>
            </ul>-->
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <ul class="sidenav" id="mobile-nav">
        <li>
            <a class="waves-effect waves-light btn-flat dark-mode-toggle" id="mobile-dark-mode-btn">
                <i class="material-icons left">brightness_4</i>
                Dark Mode
            </a>
        </li>
        <li id="mobile-auth-buttons">
            <a href="#" class="waves-effect waves-light modal-trigger" data-target="login-modal">Login</a>
            <a href="#" class="waves-effect waves-light modal-trigger" data-target="signup-modal">Sign Up</a>
        </li>
        <li id="mobile-user-buttons" style="display: none;">
            <a href="#" class="waves-effect waves-light green-text text-darken-1 modal-trigger" data-target="upload-modal">
                <i class="material-icons left">add</i>
                Post Meme
            </a>
            <a href="#" class="waves-effect waves-light red-text text-darken-1" id="mobile-logout-link">
                <i class="material-icons left">exit_to_app</i>
                Logout
            </a>
        </li>
    </ul>

    <!-- Floating Action Button for Mobile -->
    <div class="fixed-action-btn hide-on-med-and-up" id="mobile-fab" style="display: none;">
        <a href="#" class="btn-floating btn-large waves-effect waves-light green darken-1 modal-trigger" data-target="upload-modal">
            <i class="large material-icons">add</i>
        </a>
    </div>

    <!-- Main Content -->
    <main class="container">
        <div class="row" id="meme-grid">
            <!-- Memes will be loaded here dynamically -->
            <div class="col s12 center-align">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer spinner-blue-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p>Loading memes...</p>
            </div>
        </div>
    </main>

      <!-- Login Modal -->
    <div id="login-modal" class="modal">
        <div class="modal-content">
            <h4>Login</h4>
            <form id="login-form">
                <div class="input-field">
                    <input id="login-email" type="email" class="validate" required>
                    <label for="login-email">Email</label>
                </div>
                <div class="input-field">
                    <input id="login-password" type="password" class="validate" required>
                    <label for="login-password">Password</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn waves-effect waves-light blue darken-3">Login</button>
                    <a href="#" class="modal-close btn-flat waves-effect">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signup-modal" class="modal">
        <div class="modal-content">
        
        <form id="signup-form">
            <h4>Create an Account</h4>
                <div class="input-field">
                    <input type="text" id="signup-username" placeholder="Username" required><br>
                    <label for="signup-username">Username</label>
                </div>
                <div class="input-field">
                    <input type="email" id="signup-email" placeholder="Email" required><br>
                    <label for="signup-email">Email</label>
                </div>
                <div class="input-field">
                    <input type="password" id="signup-password" placeholder="Password" required><br>
                    <label for="signup-password">Password</label>
                </div>
                <div class="input-field">
                    <input type="password" id="signup-confirm-password" placeholder="Confirm Password" required><br>
                    <label for="signup-confirm-password">Confirm Password</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn waves-effect waves-light blue darken-3">Sign Up</button>
                    <a href="#" class="modal-close btn-flat waves-effect">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Meme Modal -->
    <div id="upload-modal" class="modal">
        <div class="modal-content">
            <h4>Upload a Meme</h4>
            <div id="upload-result" style="margin-top: 10px; color: green;"></div>
            <form id="upload-form" enctype="multipart/form-data" method="POST">
                <div class="file-field input-field">
                    <div class="btn blue darken-3">
                        <span>Choose Image</span>
                        <input type="file" name="meme" id="meme-image" accept="image/*" required><br><br>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
                <div class="input-field">
                <input type="text" name="caption" id="meme-caption" placeholder="Caption (optional)"><br><br>
                    <label for="meme-title">Title (optional)</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn waves-effect waves-light blue darken-3">Upload</button>
                    <a href="#" class="modal-close btn-flat waves-effect">Cancel</a>
                </div>
                <input type="hidden" name="userID" value="<?= $_SESSION['user'] ?? 0 ?>">
            </form>
        </div>

    </div>

    <!-- Share Modal -->
    <div id="share-modal" class="modal">
        <div class="modal-content">
            <h4>Share this Meme</h4>
            <div class="input-field">
                <input type="text" id="share-link" readonly>
                <label for="share-link">Meme Link</label>
            </div>
            <div class="row">
                <div class="col s6">
                    <button class="btn waves-effect waves-light blue darken-3" id="copy-link-btn"><i class="material-icons left">content_copy</i>Copy Link</button>
                </div>
                <div class="col s6">
                    <button class="btn waves-effect waves-light blue" id="share-twitter-btn"><i class="material-icons left">share</i>Twitter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>

    <script>
    $(document).ready(function () {
    $('#login-form').submit(function (e) {
        e.preventDefault();

        const email = $('#login-email').val();
        const password = $('#login-password').val();

        $.ajax({
        url: 'php/login.php',
        method: 'POST',
        data: {
            email: email,
            password: password
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
            // Show success feedback
            M.toast({ html: '✅ Login successful!', classes: 'green' });

            // Update UI
            $('#auth-buttons, #mobile-auth-buttons').hide();
            $('#user-buttons, #mobile-user-buttons, #mobile-fab').show();
            $('#username-label').text(response.user_name);
            $('#welcome-msg').show();

            // Close modal
            $('#login-modal').modal('close');
            } else {
            M.toast({ html: '❌ ' + response.message, classes: 'red' });
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            M.toast({ html: '❌ Server error.', classes: 'red' });
        }
        });
    });
    });
    </script>
    
</body>
</html>

