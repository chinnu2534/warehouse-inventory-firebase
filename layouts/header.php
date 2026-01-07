<?php $user = current_user(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php if (!empty($page_title))
    echo remove_junk($page_title);
  elseif (!empty($user))
    echo ucfirst($user['name']);
  else
    echo "Simple inventory System"; ?>
  </title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
  <link rel="stylesheet" href="libs/css/main.css" />
</head>

<body>
  <?php if ($session->isUserLoggedIn()): ?>
    <header id="header">
      <div class="logo pull-left"> OSWA - Inventory </div>
      <div class="header-content">
        <div class="header-date pull-left">
          <strong><?php echo date("F j, Y, g:i a"); ?></strong>
        </div>
        <div class="pull-right clearfix">
          <ul class="info-menu list-inline list-unstyled">
            <li class="profile">
              <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                <img src="uploads/users/<?php echo $user['image'] ?? 'default.jpg'; ?>" alt="user-image"
                  class="img-circle img-inline">
                <span><?php echo remove_junk(ucfirst($user['name'] ?? 'User')); ?> <i class="caret"></i></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="profile.php?id=<?php echo (int) $user['id']; ?>">
                    <i class="glyphicon glyphicon-user"></i>
                    Profile
                  </a>
                </li>
                <li>
                  <a href="edit_account.php" title="edit account">
                    <i class="glyphicon glyphicon-cog"></i>
                    Settings
                  </a>
                </li>
                <li class="last">
                  <a href="logout.php">
                    <i class="glyphicon glyphicon-off"></i>
                    Logout
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      </div>
      </div>
    </header>

    <!-- Professional Welcome Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="welcomeModalLabel"
      data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
          <div class="modal-header"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; padding: 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
              style="color: white; opacity: 1;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="welcomeModalLabel" style="font-weight: bold; letter-spacing: 0.5px;">
              <i class="glyphicon glyphicon-info-sign"></i> A Note on My Professional Journey
            </h4>
          </div>
          <div class="modal-body" style="padding: 30px; font-size: 16px; line-height: 1.6; color: #444;">
            <p style="margin-bottom: 20px;"><strong>Welcome! Before you review my profile, I’d like to share a brief
                context.</strong></p>

            <p>You may notice a uniquely broad range of qualifications in my background. This versatility is intentional.
            </p>

            <p>My career transition from India to the USA inspired me to master a wide spectrum of technologies—ranging
              from <strong>Full-Stack Development</strong> to advanced research in <strong>AI/ML, Quantum Technologies,
                and Cryptography</strong>.</p>

            <p style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #764ba2; border-radius: 4px;">
              <em>My goal wasn't just to be "over-qualified," but to be <strong>future-proof</strong>. This adaptability
                ensures that no matter how technology evolves, I can pivot, innovate, and contribute significant value to
                your team immediately.</em>
            </p>
          </div>
          <div class="modal-footer" style="padding: 20px; border-top: none;">
            <button type="button" class="btn btn-primary btn-lg btn-block" data-dismiss="modal"
              style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; font-weight: bold;">
              Explore My Work
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Script -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Check if user has already seen the modal using localStorage
        if (!localStorage.getItem('welcomeModalSeen')) {
          // Show modal using jQuery (Bootstrap dependency)
          setTimeout(function () {
            $('#welcomeModal').modal('show');
          }, 500); // Small delay for better UX

          // Set flag so it doesn't show again in this browser
          localStorage.setItem('welcomeModalSeen', 'true');
        }
      });
    </script>
    <div class="sidebar">
      <?php
      // DEBUG: Remove this after fixing
      echo "<!-- DEBUG: user_level=" . ($user['user_level'] ?? 'NULL') . " Type=" . gettype($user['user_level'] ?? null) . " -->";
      ?>
      <?php if (($user['user_level'] ?? '') == '1'): ?>
        <!-- admin menu -->
        <?php include_once('admin_menu.php'); ?>

      <?php elseif (($user['user_level'] ?? '') == '2'): ?>
        <!-- Special user -->
        <?php include_once('special_menu.php'); ?>

      <?php elseif (($user['user_level'] ?? '') == '3'): ?>
        <!-- User menu -->
        <?php include_once('user_menu.php'); ?>

      <?php endif; ?>

    </div>
  <?php endif; ?>

  <div class="page">
    <div class="container-fluid">