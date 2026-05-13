<?php

declare(strict_types=1);

$pageTitle = 'Trader profile — GroceryGo';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title profile-hero-title">Trader profile</h1>
  <hr class="wf-page-rule">

  <section class="profile-account-card" aria-labelledby="account-heading">
    <h2 id="account-heading" class="wf-section-title" style="text-align:center;margin-bottom:24px;">Account details</h2>
    <div class="profile-account-inner">
      <div class="profile-photo-col">
        <div class="profile-photo-placeholder">Photo</div>
        <div class="profile-actions-stack">
          <button type="button" class="btn btn-outline btn-wide">Upload picture</button>
          <button type="button" class="btn btn-outline btn-wide" style="text-transform:uppercase;letter-spacing:0.04em;font-size:13px;">Change password</button>
        </div>
      </div>
      <div>
        <div class="details-grid">
          <div class="detail-pair">
            <p class="detail-label">Trader name</p>
            <p class="detail-value">Bakery</p>
          </div>
          <div class="detail-pair">
            <p class="detail-label">Email</p>
            <p class="detail-value">bakery@gmail.com</p>
          </div>
          <div class="detail-pair">
            <p class="detail-label">Shop name</p>
            <p class="detail-value">Bakery</p>
          </div>
          <div class="detail-pair">
            <p class="detail-label">Shop address</p>
            <p class="detail-value">Downtown</p>
          </div>
          <div class="detail-pair">
            <p class="detail-label">Phone number</p>
            <p class="detail-value">0000000000</p>
          </div>
          <div class="detail-pair" style="grid-column: 1 / -1;">
            <p class="detail-label">Opening hours</p>
            <p class="detail-value">Mon–Sat: 8AM–6PM; Sun: Closed</p>
          </div>
        </div>
        <div class="profile-account-footer">
          <a class="btn btn-primary" href="profile-edit.php">Edit details</a>
          <a class="btn btn-outline" href="login.php">Logout</a>
        </div>
      </div>
    </div>
  </section>

  <p class="muted small" style="margin-top:24px;"><a href="index.php">Preview hub</a></p>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
